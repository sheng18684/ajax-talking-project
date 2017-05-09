<?php
/** 
 * 一个用于抓取网站图片的类 
 * 
 * @package default 
 * @author  WuJunwei 
 */
class catchImg{
	public $save_path;                  //抓取图片的保存地址  
  
    //抓取图片的大小限制(单位:字节) 只抓比size比这个限制大的图片  
    public $img_size=0;   
  
    //定义一个静态数组,用于记录曾经抓取过的的超链接地址,避免重复抓取         
    public static $a_url_arr=array(); 

    /** 
     * @param String $save_path    抓取图片的保存地址 
     * @param Int    $img_size     抓取图片的保存地址 
     */  
    public function __construct($save_path,$img_size,$domainUrl)  
    {  
        if(!file_exists($save_path)){
            mkdir($save_path);
        }
        $this->save_path=$save_path;  
        $this->img_size=$img_size;
        $this->domainUrl=$domainUrl; 
    }  

    /** 
     * 递归下载抓取首页及其子页面图片的方法  ( recursive 递归) 
     * 
     * @param   String  $capture_url  用于抓取图片的网址 
     *  
     */  
    public function recursive_download_images($capture_url)  
    {  

        $pageContent = @file_get_contents("$capture_url");

        preg_match_all('/<li>.*?href="(.*?)".*?<\/li>/', $pageContent, $detailUrlArr);

        foreach ($detailUrlArr[1] as &$value) {
            if ( strpos($value, 'http://')!==false ) //如果url包含http://,可以直接访问  
            {  
                $a_url = $value;  
            }else   //否则证明是相对地址, 需要重新拼凑超链接的访问地址  
            {   
                $a_url=$this->domainUrl.$value;  
            }
            $detailContent=@file_get_contents($a_url);
            
            preg_match_all('/<div class="picContent".*?>.*?src="(.*?)".*?<\/div>/', $detailContent, $imgUrlArr);
            dump($imgUrlArr);exit;
            foreach ($imgUrlArr[1] as &$val) {
                $imgcontent=@file_get_contents($val);
                file_put_contents($this->save_path.str_replace(array('-',' ',':'), array('','',''), date('Y-m-d H:i:s')).'.jpg',$imgcontent);
                
            }
        }
        // $pageContent=$this->curlGet($capture_url);          
    }


    /**
     * 获取页面内容
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function curlGet($url){
	      $ch=curl_init();
	      curl_setopt($ch,CURLOPT_HEADER,false);
	      curl_setopt($ch,CURLOPT_URL,$url);
	      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	      $content=curl_exec($ch);
	      
          if(@curl_error($content)){ 
              echo 'Curl error: ' . @curl_error($content); 
              exit;
          }

          curl_close($ch);
          return $content;

	      // return iconv('GBK','UTF-8',$content);
	}

    
    

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

set_time_limit(0);     //设置脚本的最大执行时间  根据情况设置   
$domainUrl='https://www.oschian.com';

$download_img=new catchImg('E:/picture/materi/images',0,$domainUrl);   //实例化下载图片对象 
for($i=1;$i<500;$i++){
    $download_img->recursive_download_images($domainUrl.'/htm/piclist1/'.$i.'.htm');
} 
echo "抓取完毕！";


?>