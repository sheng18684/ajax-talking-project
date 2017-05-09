<?php
/**
* 
*/
class CatchData
{
	
	function __construct()
	{
		$dbName='mysql:dbname=test;host=127.0.0.1';
		$dbUser='root';
		$dbPwd='';
		$this->con = new PDO($dbName, $dbUser, $dbPwd,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
	}


	public function index($url){
		
	    $pageContent=curlGet($url);

	    
	    /*企业分组Url*/
	    preg_match_all('/<td width="100".*?>.*?href="(.*?)".*?<\/td>/', $pageContent,$group_url);	  
	    preg_match_all('/<li class="f_gray">.*?<\/li>/', $pageContent,$titleArr);
	    
	    dump($titleArr);
	    exit;
		

	}


}

$info=new CatchData;
$domainUrl='http://www.qygq.com/company/search-htm-areaid-12-typeid--page-';
for($i=1;$i<152;$i++) { 
	$info->index($domainUrl.$i.'.html');
}
/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/**
* 获取页面内容
* @param  [type] $url [description]
* @return [type]      [description]
*/
function curlGet($url){
	$header = array( 
		'CLIENT-IP:115.239.210.27', 
		'X-FORWARDED-FOR:115.239.210.27', 
		); 
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
	curl_setopt ($ch, CURLOPT_REFERER, "https://www.baidu.com/");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$content=curl_exec($ch);

	if(@curl_error($content)){ 
		echo 'Curl error: ' . @curl_error($content); 
		exit;
	}

	curl_close($ch);
	return $content;

	      // return iconv('GBK','UTF-8',$content);
}
?>