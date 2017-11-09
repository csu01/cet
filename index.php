<?php
	   header("Content-Type: text/html; charset=gb2312");
	//判断是否有表单提交
//	if(!isset($_GET['xm']) || !isset($_GET['xh']) ){//没有表单提交
//		echo '参数错误';
//	    exit;
//	}
//  $xh =$_GET['xh'];
//	$xm = urlencode($_GET['xm']);//把提交的中文姓名改成gb2312的url编码
	
//	$name =mb_convert_encoding('蔡文杰','gb2312','utf-8' );//姓名要改编码，不然存如的数据会乱码
//  $name =$_GET['xm'];
    $name ='蔡文杰';
	$xh = '0202140121';
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
	
			//第二步，通过校园内网请求成绩
//			http://exam.csu.edu.cn/engfen.asp?xm=%C5%D3%D6%D8%BE%C1&sfzh=&zkzh=&xh=1909140126
    $url = 'http://exam.csu.edu.cn/engfen.asp?xm=%C5%D3%D6%D8%BE%C1&sfzh=&zkzh=&xh=1909140126';
//  $url = 'http://202.197.61.241/engfen.asp?xm=%B2%CC%CE%C4%BD%DC&sfzh=&zkzh=&xh=0202140121';
//	$url = 'http://202.197.61.241/engfen.asp?xm='.$xm.'&sfzh=&zkzh=&xh='.$xh; 
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
	
	if(strlen($dxycontent)<400){
		echo '<font>中南表白墙</font>提示：您的学号或者姓名输入有误';
		return;
		exit;
	}
	
	$preg = "/<tr border.*?>(.*?)<\/tr>/ism";
	preg_match_all($preg,$dxycontent,$matches);
	$list =array();
	
	$rs =$matches[0];
	for($x=0;$x<count($rs);$x++){
		$rs[$x] =strip_tags($rs[$x]);//删除结果集里面的html字符
		$list[$x] = explode(" ", $rs[$x]); 
//		$list[$x] =array_filter($list[$x]);//删除数组中的空元素
		empty($list[$x][39])?$quan =0:$quan =$list[$x][39];//如果成绩为0，则 等级13   准考证号 26  证书号39 成绩55 位置会变，所以手动设置值
		empty($list[$x][39])?$source=0:$source=$list[$x][55];
		$level = $list[$x][13];
		$cetn = $list[$x][26];
		//写入每条数据到数据库
		$sql = "INSERT INTO `cet2018`(`name`,`source`,`snum`,`level`,`cetn`,`quan`) VALUES('$name','$source','$xh','$level','$cetn','$quan') ";  
//	    $r = $test->insert($sql);
//		if(!$r){
//			echo '<font>中南表白墙</font>提示：服务器被挤爆了，请稍后尝试';
//		    return;
//		    exit;
//		}
	}
	
	for($x=0;$x<count($list);$x++){
		$cet[$x][0]=$list[$x][13];
		$cet[$x][1]=empty($list[$x][55])?0:$list[$x][55];
	}
	
	$params_json = json_encode($cet);
	echo $params_json;
	exit;
	
    echo "<pre>";
    print_r($cet);
    echo "<pre>";
	
	//$list[$x]  里面  等级13   准考证号 26  证书号39 成绩55
	
