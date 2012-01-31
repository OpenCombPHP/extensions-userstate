<?php
namespace org\opencomb\userstate ;

class State
{
    private $aTemplates = array(
    	'blog' => array(
    			'title_template'=>'{actor} 发表了新日志' ,
    			'body_template'=>'<strong>{subject}</strong><br />{summary}' ,
    	) ,
    	'album' => array(
    			'title_template'=>'{actor} 上传了新图片至相册 {album}' ,
    			'body_template'=>'<div class="clear">共 {picnum} 张图片</div>' ,
    	) ,
    	'doing' => array(
    			'title_template'=>'{actor}：{subject}' ,
    			'body_template'=>'' ,
    	) ,
    	'thread' => array(
    			'title_template'=>'{actor} 在 {thread} 发起了新话题 ' ,
    			'body_template'=>'<b>{subject}</b><br>{summary}' ,
    	) ,
    	'blog_comment' => array(
    			'title_template'=>'{actor} 评论了 {touser} 的日志 {blog}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    	'doing_comment' => array(
    			'title_template'=>'{actor} 回复了 {touser} 的心情碎语 {doing}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    	'thread_comment' => array(
    			'title_template'=>'{actor} 回复了 {touser} 的话题 {thread}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    ) ;
    
    public function getTemplate($system)
    {
        return $this->aTemplates[$system];
    }
    
    public function getTitleTemplate($system)
    {
        return $this->aTemplates[$system]["title_template"];
    }
    
    public function getBodyTemplate($system)
    {
        return $this->aTemplates[$system]["body_template"];
    }
	
    public function getStateHtml($sTemplate,$aParams)
    {
        preg_match_all("/\{(.*?)\}/", $sTemplate, $aTemplate);
        
        for($i = 0; $i < sizeof($aTemplate[0]); $i++){
            $aData[] =  $aParams[$aTemplate[1][$i]];
        }
        
        $html = str_replace($aTemplate[0], $aData,$sTemplate);
        return $html;
    }
}

?>