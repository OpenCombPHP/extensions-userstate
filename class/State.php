<?php
namespace org\opencomb\userstate ;

class State
{
    private $aTemplates = array(
    	'blog' => array(
    			'title_template'=>'{actor} 发表了新日志' ,
    			'body_template'=>'<strong>{title}</strong><br />{body}' ,
    	) ,
    	'album' => array(
    			'title_template'=>'{actor} 上传了新图片至相册 {article_title}' ,
    			'body_template'=>'' ,
    	) ,
    	'doing' => array(
    			'title_template'=>'{actor}：{title}' ,
    			'body_template'=>'' ,
    	) ,
    	'thread' => array(
    			'title_template'=>'{actor} 在 {article_title} 发起了新话题 ' ,
    			'body_template'=>'<b>{title}</b><br>{body}' ,
    	) ,
    	'blog_comment' => array(
    			'title_template'=>'{actor} 评论了 {touser} 的日志 {article_title}' ,
    			'body_template'=>'{body}' ,
    	) ,
    	'doing_comment' => array(
    			'title_template'=>'{actor} 回复了 {article_uid} 的心情碎语 {article_title}' ,
    			'body_template'=>'{body}' ,
    	) ,
    	'thread_comment' => array(
    			'title_template'=>'{actor} 回复了 {article_uid} 的话题 {article_title}' ,
    			'body_template'=>'{body}' ,
    	) ,
    ) ;
    
    public function getTemplate($system)
    {
        return $this->aTemplates[$system];
    }
	
    public function getStateHtml($type,$o)
    {
        //$sTemplate = @$this->aTemplates[$o->system][$type."_template"];
        $html = "";
        
        /*
        if(!empty($sTemplate))
        {
            preg_match_all("/\{(.*?)\}/", $sTemplate, $aTemplate);
            
            $aData_tmp["actor"] = $o->child("info")->nickname;
            $aData_tmp["subject"] = $o->subject;
            $aData_tmp["body"] = $o->body;
            $aData_tmp["article_title"] = $o->article_title;
            $aData_tmp["article_uid"] = $o->child("toinfo")->nickname;
            
            
            for($i = 0; $i < sizeof($aTemplate[0]); $i++){
                $aData[] =  $aData_tmp[$aTemplate[1][$i]];
            }
            
            if($aTemplate[0] && $aData)
            {
                $html = str_replace($aTemplate[0], $aData,$sTemplate);
            }
        }else{*/
            if($type == "title"){
                $html = $o->title;
            }
            if ($type == "body"){
                $html = $o->body;
            }
        /*}*/
       
        

        return $html;
    }
}

?>