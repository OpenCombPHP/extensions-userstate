<?php
namespace org\opencomb\userstate ;

use org\opencomb\coresystem\mvc\controller\UserSpace;

use com\wonei\woneibridge\aspect\NamecardAspect;
use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class ListState extends UserSpace
{
	public function createBeanConfig()
	{
	    $aOrm = array(
		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:state' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,     
            			'hasMany:astate' => array(    //一对一
            				'table' => 'oauth:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
        		            'keys'=>array('service','sid'),
            			) , 
        		        'hasOne:user' => array(    //一对一
        		                'table' => 'coresystem:user',
        		                'fromkeys'=>'uid',
        		                'tokeys'=>'uid',
        		                //'columns' => '*' ,
        		        ) ,
        		        'hasOne:info' => array(    //一对一
        		                'table' => 'coresystem:userinfo',
        		                'fromkeys'=>'uid',
        		                'tokeys'=>'uid',
        		                //'columns' => '*' ,
        		        ) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_attachment',
                		),
    			        'hasOne:subscription'=>array(    //一对多
    			                'keys'=>array('from','to') ,
    			                'fromkeys'=>'uid',
    			                'tokeys'=>'to',
    			                'table'=>'friends:subscription',
    			        ),
            		) ,
                    'list'=>true,
            ) ,
	            
            /**
             * 用来快速获取，判断认证信息
             */
            'model:auser' => array(
                    'orm' => array(
                            'table' => 'oauth:user' ,
                            'keys'=>array('uid','suid'),
                    ) ,
                    'list' => true,
            ) ,
	        
	        

			// 视图
			'view' => array(
				/**
				 * 'params' => array('pageNum'=>'2'),
				 * 控制器传参数
				 */
// 				'params' => array('pageNum'=>'30'),
				'template' => 'userstate:UserState.html' ,
				'model' => 'state' ,
			) ,
		) ;
	    
	    
	    if( $this->params["channel"] == "friends")
	    {
	        $aId = $this->requireLogined() ;
	        
	        $aOrm['model:state'] = array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,  
            			'hasMany:astate' => array(    //一对一
            				'table' => 'oauth:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
        		            'keys'=>array('service','sid'),
            			) , 
        		        'hasOne:user' => array(    //一对一
        		                'table' => 'coresystem:user',
        		                'fromkeys'=>'uid',
        		                'tokeys'=>'uid',
        		                //'columns' => '*' ,
        		        ) ,
        		        'hasOne:info' => array(    //一对一
        		                'table' => 'coresystem:userinfo',
        		                'fromkeys'=>'uid',
        		                'tokeys'=>'uid',
        		                //'columns' => '*' ,
        		        ) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_attachment',
                		),
    			        'hasOne:subscription'=>array(    //一对多
    			                'keys'=>array('from','to') ,
    			                'fromkeys'=>'uid',
    			                'tokeys'=>'to',
    			                'table'=>'friends:subscription',
    			        ),
            		) ,
                    'list'=>true,
            );
	        
	        $aUid = array();
	        foreach (IdManager::singleton()->iterator() as $v){
	            $aUid[] = $v->userId();
	        }
	        if(count($aUid) > 1)
	        {
	        	$sSql = '';
	        	foreach($aUid as $nKey => $nUid)
	        	{
	        		if($nKey)
	        		{
	        			$sSql .= ',';
	        		}
	        		$sSql.='@'.($nKey+1);
	        	}
	        	$aUid[] =  $aId->userId();
	            $aOrm['model:state']['orm']['where'] = array( "subscription.from in ( {$sSql} ) or uid = @" . count($aUid)+1 , $aUid );
	        }else{
	            $aOrm['model:state']['orm']['where'] = array( 'subscription.from = @1 or uid = @2' , $aId->userId() , $aId->userId() );
	        }
	    }
	    
	    
	    if( $this->params["channel"] == "wownei")
	    {
	        //$aId = $this->requireLogined() ;
	        $aOrm['model:state'] = array(
	                'class' => 'model' ,
	                'orm' => array(
	                        'table' => 'userstate:state' ,
	                        'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,
                			'hasMany:astate' => array(    //一对一
                				'table' => 'oauth:state',
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
            		            'keys'=>array('service','sid'),
                			) , 
            		        'hasOne:user' => array(    //一对一
            		                'table' => 'coresystem:user',
            		                'fromkeys'=>'uid',
            		                'tokeys'=>'uid',
            		                //'columns' => '*' ,
            		        ) ,
            		        'hasOne:info' => array(    //一对一
            		                'table' => 'coresystem:userinfo',
            		                'fromkeys'=>'uid',
            		                'tokeys'=>'uid',
            		                //'columns' => '*' ,
            		        ) ,
	                        'hasMany:attachments'=>array(    //一对多
	                                'fromkeys'=>'stid',
	                                'tokeys'=>'stid',
	                                'table'=>'userstate:state_attachment',
	                        ),
	                        'hasOne:subscription'=>array(    //一对多
	                                'keys'=>array('from','to') ,
	                                'fromkeys'=>'uid',
	                                'tokeys'=>'to',
	                                'table'=>'friends:subscription',
	                        ),
	                        'where' => array( 'stid not like @1',"pull|%") ,
	                ) ,
	                'list'=>true,
	        );
	    
	    }
	    $aOrm['model:state']['orm']['orderDesc'] = 'time';
	    
	    return  $aOrm;
	}
	
	public function process()
	{
	    /**
	     * @wiki /CoreSystem
	     * 用户系统
	     */
	    /**
	     * @example 获得登陆信息（未登陆自动跳转到登陆界面）:name[1]
	     * @forwiki /CoreSystem
	     * 获得登陆信息（未登陆自动跳转到登陆界面）
	     */
	    {
// 	        $aId = $this->requireLogined() ;
	    }
	    
	    /**
	     * @example 获得登陆信息:name[1]
	     * @forwiki /CoreSystem
	     * 获得登陆信息
	     */
	    {
	        //$aId = IdManager::singleton()->currentId() ;
	    }
	    
	    $oState = new State();
	    $sSql = '';
	    $arrParamsForSql = array();
	    
        if($this->params["system"])
        {
            $sSql.= 'system = @1';
            $arrParamsForSql[] = $this->params["system"];
        }
        if($this->params["sex"])
        {
        	$nSqlNum = 1;
			if(!empty($sSql)){
				$sSql.= ' and ';
				$nSqlNum = 2;
			}
            $sSql.= 'info.sex = @' . $nSqlNum;
            $arrParamsForSql[] = $this->params["sex"];
        }
       
        /*测试用: 只显示某网站的数据*/
//         $this->state->prototype()->criteria()->where()->like('stid','pull|renren.com%');
        ////////////////////////////////////////////////
        
        
        //默认30个条目
        $nPageNum = 30;
        if($this->params()->has("pageNum")){
        	$nPageNum = $this->params()->int("pageNum");
        }
        $nPageNum = $this->params['limitlen']?$this->params['limitlen']:$nPageNum;
        
        $this->state->setPagination($nPageNum,$this->params['limitfrom']?Ceil(($this->params['limitfrom']/$nPageNum)+1):1);
        
        
        $t = microtime(1) ;
	    $this->state->loadSql($sSql,$arrParamsForSql) ;
	    
	    //DB::singleton()->executeLog() ;
	    foreach($this->state->childIterator() as $k => $o)
	    {
	        if(!$o->title)
	        {
	            $o->setData("title",$o->body);
	            $o->setData("body","");
	        }
	        preg_match("/pull\|(.*?)\|/", $o->stid,$aService);
	        if($aService){
	        	$o->setData("service",$aService['1']);
	        }else{
	        	$o->setData("service",'wownei');
	        }
	        $o->setData("title",$this->filterLink($o->title,$o->service));
	        $o->setData("title_nolink",preg_replace("/<a .*?>(.*?)<\/a>/u", "$1", $o->title));
	        $o->setData("attachmentsFilterArray",$this->filterAttachments($o->child('attachments')));
	        
	        if($o->forwardtid)
	        {
	            $oStateClone = $this->state->prototype()->createModel(true);
// 	            $oStateCloneCriteria = $oStateClone->createCriteria();
//                 $oStateCloneCriteria->where()->clear();
//                 $oStateCloneCriteria->where()->eq('stid',$o->forwardtid);
//                 $oStateCloneCriteria->setLimit(1);
	            $oStateClone->loadSql("`stid` = @1 " , array($o->forwardtid));
	            
	            if($oStateClone->childrenCount() > 0)
	            {
	                foreach($oStateClone->childIterator() as $oClone)
	                {
	                    if(!$oClone->title)
	                    {
	                        $oClone->setData("title",$oClone->body);
	                        $oClone->setData("body","");
	                    }
	                    preg_match("/pull\|(.*?)\|/", $oClone->stid,$aService2);
	                
	                    if($aService2){
	                        $oClone->setData("service",$aService2['1']);
	                    }else{
	                        $oClone->setData("service",'wownei');
	                    }
	                    $oClone->setData("title",$this->filterLink($oClone->title,$oClone->service));
	                    $oClone->setData("title_nolink",preg_replace("/<a .*?>(.*?)<\/a>/u", "$1", $oClone->title));
	                    $oClone->setData("attachmentsFilterArray",$this->filterAttachments($oClone->child('attachments')));
	                }
	                
	                $o->addChild($oStateClone,'source');
	            }
	        }
	        
	        /**
	         * 
	         */
	        
            $aLastData[$o->service]['time'] =  $o->time;
            $aLastData[$o->service]['id'] =  @$o->child('astate')->child(0)->sid;
            $aData = json_decode($o->data,true);
            $aLastData[$o->service]['max_id'] =  @$aData['cursor_id'];
            @$aLastData[$o->service]['num'] ++;
	            
	            
            if($k == $this->state->childrenCount()-1)
	        {
	            $o->setData("lastData",$aLastData);
	        }
	        
	    }
	    /**
	     * 获得oauth信息，以后放到oauth扩展
	     */
	    
	    if( $aId=IdManager::singleton()->currentId() )
	    {
	        $this->auser->load($aId->userId(),'uid') ;
	    }
	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     * 
	     * 打印sql
	     * DB::singleton()->executeLog() ;
	     */
	}
	
	
	function filterAttachments($oAttachments)
	{
	    foreach($oAttachments->childIterator() as $o)
	    {
	        $aRs = array();
	        if(@$o->type)
	        {
	            $a['aid'] = @$o->aid;
	            $a['stid'] = @$o->stid;
	            $a['type'] = @$o->type;
	            $a['title'] = @$o->title;
	            $a['url'] = @$o->url;
	            $a['thumbnail_pic'] = @$o->thumbnail_pic;
	            $a['link'] = @$o->link;
	            $aRs[$o->type][] = $a;
	        }
	        return $aRs;
	    }
	    return @$aRs;
	}
	
	function filterLink($str,$service)
	{
	    //去掉现有然A
	    $str = preg_replace("/<a.*?>(.*)>/u", "$1", $str);
	    
	    //增加#
	    $str = preg_replace("/#(.*)#/u", "<a href='/?c=org.opencomb.userstate.Tag&tag=$1'>#$1#</a>", $str);
	    
	    //增加@
	    $str = preg_replace("/@(.*?)[：| ]/u", '<a href="javascript:;">@$1</a>', $str);
	    
	    //增加A
	    $str = preg_replace(array("/http:\/\/(.*?) /u","/ http:\/\/(.*)$/u"), array("<a href='http://$1'>http://$1</a>","<a href='http://$1'>http://$1</a>"), $str);
	    
	    return $str;
	}
	
	/**
	 * 如果是半小时内,就显示:"xx分钟以前",如果是半小时以后,就显示日期时间
	 * @param int $nTime
	 * @return string 时间
	 */
	public function getCreateTime($date)
	{
	    $limit = time() - $date;
        if($limit < 60)
        {
            return $limit . '秒钟之前';
        }
         if($limit >= 60 && $limit < 3600)
        {
         return floor($limit/60) . '分钟之前';
        }
        if($limit >= 3600 && $limit < 86400)
        {
         return floor($limit/3600) . '小时之前';
        }
        if($limit >= 86400 and $limit<259200)
        {
         return floor($limit/86400) . '天之前';
         }
        if($limit >= 259200)
        {
           return date('Y-m-d H:i:s', $date);
        }else{
          return '';
         }
	}
}
