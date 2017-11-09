<?php
    header("Content-Type: text/html; charset=utf-8");
	//判断是否有表单提交
	if(!isset($_POST['xm']) || !isset($_POST['xh']) ){//没有表单提交
		echo '参数错误';
	    exit;
	}
    $xh =$_POST['xh'];
	$xm = urlencode(mb_convert_encoding($_POST['xm'],'gb2312','utf-8' ));//把提交的中文姓名改成gb2312的url编码
	
	//第一步，根据请求先查询2018年的数据库里面是否有记录，有的话直接返回数据库的结果
		//使用pdo连接上数据库	
	define('DB_HOST', '120.78.66.42');  
    define('DB_USER', 'csuwk');  
    define('DB_PWD', 'CAI123421');  
    define('DB_NAME', 'cet');  
  
    class DBPDO {  
  
        private static $instance;         
        public $dsn;         
        public $dbuser;         
        public $dbpwd;         
        public $sth;         
        public $dbh;   
  
        //初始化  
        function __construct() {  
            $this->dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;  
            $this->dbuser = DB_USER;  
            $this->dbpwd = DB_PWD;  
            $this->connect();  
            $this->dbh->query("SET NAMES 'UTF8'");  
            $this->dbh->query("SET TIME_ZONE = '+8:00'");  
        }  
  
        //连接数据库  
        public function connect() {  
            try {  
                $this->dbh = new PDO($this->dsn, $this->dbuser, $this->dbpwd);  
            }  
            catch(PDOException $e) {  
                exit('连接失败:'.$e->getMessage());  
            }  
        }  
  
        //获取表字段  
        public function getFields($table='cet2018') {  
            $this->sth = $this->dbh->query("DESCRIBE $table");  
            $this->getPDOError();  
            $this->sth->setFetchMode(PDO::FETCH_ASSOC);  
            $result = $this->sth->fetchAll();  
            $this->sth = null;  
            return $result;  
        }  
  
        //插入数据  
        public function insert($sql) {  
            if($this->dbh->exec($sql)) {  
                $this->getPDOError();  
                return $this->dbh->lastInsertId();  
            }  
            return false;  
        }  
  
        //删除数据  
        public function delete($sql) {  
            if(($rows = $this->dbh->exec($sql)) > 0) {  
                $this->getPDOError();  
                return $rows;  
            }  
            else {  
                return false;  
            }  
        }  
  
        //更改数据  
        public function update($sql) {  
            if(($rows = $this->dbh->exec($sql)) > 0) {  
                $this->getPDOError();  
                return $rows;  
            }  
            return false;  
        }  
  
        //获取数据  
        public function select($sql) {  
            $this->sth = $this->dbh->query($sql);  
            $this->getPDOError();  
            $this->sth->setFetchMode(PDO::FETCH_ASSOC);  
            $result = $this->sth->fetchAll();  
            $this->sth = null;  
            return $result;  
        }  
  
        //获取数目  
        public function count($sql) {  
            $count = $this->dbh->query($sql);  
            $this->getPDOError();  
            return $count->fetchColumn();  
        }  
  
        //获取PDO错误信息  
        private function getPDOError() {  
            if($this->dbh->errorCode() != '00000') {  
                $error = $this->dbh->errorInfo();  
                exit($error[2]);  
            }  
        }  
  
        //关闭连接  
        public function __destruct() {  
            $this->dbh = null;  
        }  
    }
	$test = new DBPDO;	
	
	    //在数据库里面查询需要学号
	$sql = "SELECT `source`,`level` FROM `cet2018` WHERE `snum`=$xh ";  
	$rs = $test->select($sql);
	if($rs){//有数据就输出
	    echo json_encode($rs);
	    exit;
//		echo "<pre>";
//	    print_r($rs);
//	    echo "<pre>";
//	    exit;
	}
	
	
	//第二步，若数据库没有数据，那么通过映射到校园内网来获取
	$url = 'http://113.246.130.217?xm='.$xm.'&'.'xh='.$xh; 
	$UserAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 UBrowser/6.2.3831.3 Safari/537.36';
    $referer='http://exam.csu.edu.cn';
	$header_ip = array( 'CLIENT-IP:8.8.8.8', 'X-FORWARDED-FOR:8.8.8.8', );
    $header = array();
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	//伪造来源referer 在HTTP请求中包含一个'referer'头的字符串。告诉服务器我是从哪个页面链接过来的，服务器籍此可以获得一些信息用于处理。
	curl_setopt($ch,CURLOPT_REFERER,$referer); 
	//伪造来源ip 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_ip);
    curl_setopt($ch, CURLOPT_COOKIE,'ASPSESSIONIDSABQRQQB=IFHJJGEDCELDJMADLCJDGAHB');
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
	//加上这个表示执行curl_exec是把输出做为返回值,不会输出到浏览器      return transfer参数
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10); 
	$dxycontent = curl_exec($ch);
	curl_close($ch);
	
	echo($dxycontent); 
?>