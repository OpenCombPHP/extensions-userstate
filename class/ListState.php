<?php
namespace org\opencomb\userstate ;

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
            			'hasOne:info' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
                            //'columns' => '*' ,        
            			) ,  
            			'hasOne:toinfo' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'article_uid',
            				'tokeys'=>'uid',
                            //'columns' => '*' , 
            			) ,
//             			'hasOne:auser' => array(    //一对一
//             				'table' => 'oauth:user',
//             	            'keys'=>array('uid','suid'),
//             				'fromkeys'=>'uid',
//             				'tokeys'=>'uid',
//             			) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_attachment',
                		),
//             			'where' => array(
//             				array('eq','stid',"23") ,
//             			) ,
            		) ,
                    'list'=>true,
            ) ,

		    /**
		     * frame
		     * frameview 子视图。子视图包含父视图。
		     */

//             'frame' => array(
            		
//                     'frameview' => array(
//                             'template' => 'userstate:ListState.html' ,
//                             /**
//                              * 给视图变量
//                              * 'vars' => array('pageNum'=>'2'),
//                              */
//                     )
//             ) ,
	            
//     		'params' => array('pageNum'=>'30'),
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
	    
	    
	    $aId = IdManager::singleton()->currentId() ;
	    if( $aId and $this->params["channel"] == "friends")
	    {
	        $aOrm['model:state'] = array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,  
            			'hasOne:info' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
                            //'columns' => '*' ,  
            			) ,
            			'hasOne:toinfo' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'article_uid',
            				'tokeys'=>'uid',
            			) ,
//             		    'hasOne:auser' => array(    //一对一
//             				'table' => 'oauth:user',
//             	            'keys'=>array('uid','suid'),
//             				'fromkeys'=>'uid',
//             				'tokeys'=>'uid',
//             			) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_attachment',
                		),
    			        'hasMany:subscription'=>array(    //一对多
    			                'keys'=>array('from','to') ,
    			                'fromkeys'=>'uid',
    			                'tokeys'=>'to',
    			                'table'=>'friends:subscription',
    			        ),
            			'where' => array(
            				// 'logic' => 'and' , (可省略)
            				array('eq','subscription.from',$aId->userId()) ,
            			) ,
            		) ,
                    'list'=>true,
            );
	        
	        
	        
	        
	        /*array(
	        		
            		// 'class' => 'model' ,	(可省略)
                    'list'=>true,
	        		
            		'orm' => array(
            			'table' => 'friends:subscription' ,
            	        'keys'=>array('from','to') ,
            			'columns' => array() ,
            			'where' => array(
            				// 'logic' => 'and' , (可省略)
            				array('eq','from',$aId->userId()) ,
            			) ,
            	        
            			//一对多
                		'hasOne:state'=>array(
            		        'table'=>'state',
            				'fromkeys'=>'to',
            				'tokeys'=>'uid',
                			'join'=>'inner',
                			
                			//一对一
            		        'hasOne:info' => array(
	            		        'table' => 'coresystem:userinfo',
	            		        'fromkeys'=>'uid',
	            		        'tokeys'=>'uid',
            		        ) ,
                			
                			//一对多
              		        'hasMany:attachments'=>array(
	            		        'table'=>'userstate:state_attachment',
	            		        'fromkeys'=>'stid',
	            		        'tokeys'=>'stid',
    		                )
                		),
            				
            			// 字段的别名
            			'alias' => array(
            					'system' => 'state.system' ,
            					'uid' => 'state.uid' ,
            					'time' => 'state.time' ,
            					'title_data' => 'state.title_data' ,
            					'body_template' => 'state.body_template' ,
            					'body_data' => 'state.body_data' ,
            					'client' => 'state.client' ,
            					'stid' => 'state.stid' ,
            				
            					'attachments.type' => 'state.attachments.type' ,
            					'attachments.url' => 'state.attachments.url' ,
            					'attachments.link' => 'state.attachments.link' ,
            				   	
            					'info.nickname' => 'state.info.nickname' ,
            					'info.sex' => 'state.info.sex' ,
            			),
            		) ,
            );
            */
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
	        $aId = $this->requireLogined() ;
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
        $nPageNum = 1;
        if($this->params()->has("pageNum")){
        	$nPageNum = $this->params()->int("pageNum");
        }
        $this->state->prototype()->criteria()->setLimit($this->params['limitlen']?$this->params['limitlen']:$nPageNum,$this->params['limitfrom']?$this->params['limitfrom']:0);
	        
	    $this->state->load() ;
	    
	    foreach($this->state->childIterator() as $o)
	    {
	        $o->setData("title_html",$oState->getStateHtml("title",$o));
	        $o->setData("body_html",$oState->getStateHtml("body",$o));
	        preg_match("/(.*?)\|/", $o->stid,$aService);
	        
	        $o->setData("service",$aService['1']);
	        
	        if($o->forwardtid)
	        {
	            $oStateClone = clone $this->state;
	            $oStateClone->clearData();
	            $oStateClone->prototype()->criteria()->where()->eq("stid",$o->forwardtid);
	            $oStateClone->load() ;
	            foreach($oStateClone->childIterator() as $oClone)
	            {
	                $oClone->setData("title_html",$oState->getStateHtml("title",$oClone));
	                $oClone->setData("body_html",$oState->getStateHtml("body",$oClone));
	                preg_match("/(.*?)\|/", $oClone->stid,$aService2);
	            
	                $oClone->setData("service",$aService2['1']);
	            }
	            
	            
	            $o->addChild($oStateClone,'source');
	        }
	    }
	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     * 
	     * 打印sql
	     * DB::singleton()->executeLog() ;
	     */
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
