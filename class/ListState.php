<?php
namespace org\opencomb\userstate ;

use com\wonei\woneibridge\aspect\NamecardAspect;

use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class ListState extends Controller
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
            			'where' => array(
                                "or",
                                array('eq','subscription.from',$aId->userId()) ,
                                array('eq','uid',$aId->userId()) ,
            			) ,
            		) ,
                    'list'=>true,
            );
	        
	        
	        
	    }
	    
	    
	    if( $this->params["channel"] == "wownei")
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
	                        'where' => array(
                                    array('notLike','stid',"pull|%") ,
	                                array(
                                        "or",
                                        array('eq','uid',$aId->userId()) ,
                                        array('eq','subscription.from',$aId->userId()),
                                    ),
	                        ) ,
	                ) ,
	                'list'=>true,
	        );
	    
	    }
	    
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
	    
	    /**
 	     * @example sql操作
	     * @forwiki /mvc/模型/模型(Model)
	     */
	    {
        $this->state->prototype()->criteria()->addOrderBy('time',true);
        //$this->state->prototype()->criteria()->where()->eq('uid',$aId->userId());
	    }
        if($this->params["system"])
        {
            $this->state->prototype()->criteria()->where()->eq('system',$this->params["system"]);
        }
        if($this->params["sex"])
        {
            $this->state->prototype()->criteria()->where()->eq('info.sex',$this->params["sex"]);
        }
        //默认30个条目
        $nPageNum = 30;
        if($this->params()->has("pageNum")){
        	$nPageNum = $this->params()->int("pageNum");
        }
        $this->state->prototype()->criteria()->setLimit($this->params['limitlen']?$this->params['limitlen']:$nPageNum,$this->params['limitfrom']?$this->params['limitfrom']:0);
        
        $t = microtime(1) ;
	    $this->state->load() ;
	    
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
	            $oStateCloneCriteria = $oStateClone->createCriteria();
                $oStateCloneCriteria->where()->clear();
                $oStateCloneCriteria->where()->eq('stid',$o->forwardtid);
                $oStateCloneCriteria->setLimit(1);
	            $oStateClone->load($oStateCloneCriteria);
	            
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
	     //$this->state->printStruct();
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
            
	    }
	    return $aRs;
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
	 * @example /MVC模式/模型/Bean配置/Where条件
	 * 
	 * （这个方法不会被执行，它只是做为 model/orm 的 bean config 数组的例子）
	 */
	private function exampleBeanOrmWhere()
	{
		// 定一个 model 的 bean config
		$arrConfig = array(
			'class' => 'model' ,
			'list' => true ,
			'orm' => array(
				'table' => 'userstate:some_table_name' ,	// 数据表
				'keys' => 'id' ,				// 数据表主键
				'columns' => array('column_a','column_b','column_c') ,	// 返回字段
				'order' => array('column_e','column_f') ,
				'limit' => 10 ,
				'where' => array(
					
					// where数组中的第一项元素如果字符串，则表示条件之间的逻辑关系，缺省为 and
					'and' , 
						
					// where 数组中的每一项array元素都做为一项sql条件表达式
					// array内的第一个元素必须时字符串格式，做为条件表达式的运算符(Restriction 类的方法名称)，
					// 后面的元素是参与运算的内容
					array('eq','column_a','abc') ,		// column_a='abc'
					array('gt','column_b',123) ,		// column_b>123
						
					// restriction 类型的 array，是一个条件分组，
					// 第二个元素必须是一个数组，其结构跟 where 数组完全一致（where - restriction 构成了一个无穷递归的结构）。
					array( 'restriction' , array(
						'or' ,
						array('le','column_c',56) ,		// column_c<=56
						array('ge','column_d',789) ,	// column_d>=789
					) )
				)
			)
		) ;
		
		// 用 bean config 数组创建 model 对象
		$aModel = BeanFactory::singleton()->createBean($arrConfig) ;
		
		// model 载入数据
		$aModel->load() ;
		
		// 打印sql执行日志，输出：
		// select column_a, column_b, column_c from some_table_name  
		// 		where column_a='abc' and column_b>123 and (
		//			column_c<=56 or column_d>=789
		//		)
		//		order by column_e desc, column_f desc
		// 		limit 20 ;
		DB::singleton()->executeLog() ;
	}
}
