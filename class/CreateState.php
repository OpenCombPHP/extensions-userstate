<?php
namespace org\opencomb\userstate ;

use org\opencomb\oauth\api\PushState;

use org\jecat\framework\lang\Exception;

use org\jecat\framework\mvc\controller\Request;

use org\opencomb\coresystem\auth\Id;

use org\jecat\framework\db\DB;

use org\jecat\framework\mvc\view\DataExchanger;

use org\jecat\framework\db\ExecuteException;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class CreateState extends Controller
{
	public function createBeanConfig()
	{
		return array(
			// 模型
            'model:state' =>  array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
            	        'keys'=>array('stid') ,
            	        'alias' => array(
            	                'info.nickname' => 'nickname' ,
            	        ),
            			'hasOne:info' => array(    //一对一
            				'table' => 'coresystem:userinfo',
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
            			) ,
                		'hasMany:attachments'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'keys'=>'aid' ,
                		        'table'=>'userstate:state_attachment',
                		),
                		'hasMany:at'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
            	                'keys'=>array('stid','username') ,
                		        'table'=>'userstate:state_at',
                		),
                		'hasMany:tag'=>array(    //一对多
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
                		        'table'=>'userstate:state_tag',
                		),
            		) ,
            ) ,
		        
	        'model:user' => array(
	                'orm' => array(
	                        'table' => 'coresystem:user' ,
	                ) ,
	        ) ,
		        
			// 视图
			'view:stateForm' => array(
				'template' => 'userstate:CreateState.html' ,
				'model' => 'state' ,
			) ,
		) ;
	}
	
	public function process()
	{
		//如果没有数据传来,只显示表单
		if(!$this->params['title'] && !$this->params['body'] && !$this->params['attachment']){
			return;
		}
		
		
		
		
		$arrAttachment = array();
		if( Request::isUserRequest($this->params) ){//用户提交来的表单
			$this->state->setData('system',NULL) ;  //防止作弊
			$this->state->setData('forwardtid',0);
			$this->state->setData('uid',IdManager::singleton()->currentId()->userId()) ;
			$this->state->setData('time',time()) ;
			//分离附件链接
			$arrBodyResult = $this->getUrls($this->params['body']);
			$this->state->setData("body",$arrBodyResult[0]);
			if($arrBodyResult[1]){
				foreach($arrBodyResult[1] as $sUrl){
					$arrAttachment[$sUrl] = array('url'=>$sUrl,'type'=>'');
				}
			}
			
			
			/**
			 * push weibo
			 * @var unknown_type
			 */
			$aWeibo = $this->params['pushweibo'];
			
			$aParams = array(
			        'service'=>$aWeibo,
			        'title'=>$this->params['body'],
			);
			$oOauthPush = new PushState($aParams);
			$oOauthPush->process();
			
			
			
		}else{ //系统内部直接保存数据
			$this->state->setData("forwardtid",$this->params['forwardtid']);
			$this->state->setData("stid","pull|".$this->params['stid']);
			$this->state->setData('system',$this->params['system']) ;
			if($this->params->has('uid')){
				$this->state->setData('uid',$this->params['uid']) ;
			}else{
				throw new Exception('没有指定uid,无法保存数据');
			}
			$this->state->setData("title",$this->params['title']);
			$this->state->setData("body",$this->params['body']);
			if($this->params['time']){
				$this->state->setData('time',$this->params['time']) ;
			}else{
				$this->state->setData('time',time()) ;
			}
			$this->state->setData("data",$this->params['data']);
			$this->state->setData("client",$this->params['client']);
			$this->state->setData("client_url",$this->params['client_url']);
			
			for($i = 0; $i < count($this->params['attachment']); $i++){
				$arrAttachmentFromParams['url'] =$this->params['attachment'][$i]['url'];
				$arrAttachmentFromParams['link'] =@$this->params['attachment'][$i]['link'];
// 				$arrAttachmentFromParams['type'] =$this->params['attachment'][$i]['type']; 
				$arrAttachmentFromParams['type'] =''; //舍弃了原先的type,我们自行判断type
				$arrAttachmentFromParams['thumbnail_pic'] =@$this->params['attachment'][$i]['thumbnail_pic'];
				$arrAttachmentFromParams['title'] =@$this->params['attachment'][$i]['title'];
				$arrAttachment[$arrAttachmentFromParams['url']] = $arrAttachmentFromParams;
			}
		}
		
		//媒体鉴别
		if($arrAttachment){
			$arrAttachment = $this->getUrlType($arrAttachment);
			foreach($arrAttachment as $sKey => $arrAttachment){
				$this->state->child("attachments")->createChild()
				->setData("url",$arrAttachment['url'])
				->setData("link",@$arrAttachment['link'])
				->setData("type",$this->getDisplayType($arrAttachment['type']))
				->setData("thumbnail_pic",@$arrAttachment['thumbnail_pic'])
				->setData("title",@$arrAttachment['title']) ;
			}
		}
		
		
		
		//at
		$title = $this->params['title'];
		preg_match_all("/@(.*?)[ |:|：|<]|@(.*)$/u", $title, $aTitle);
		
		$aTitle = array_filter(array_unique(array_merge($aTitle[1],$aTitle[2])));
	    
        foreach ($aTitle as $v){
    
            $uid = 0;
            //测试用户是否存在
            
            $this->user->load($this->params['service']."#".trim($v),'username') ;
            
            if($this->user->uid)
            {
                $uid = $this->user->uid;
            }
    
            $this->state->child("at")->createChild()
            ->setData("username",trim($v))
            ->setData("uid",$uid) ;
        }
		
		
		//tag
		preg_match_all("/#(.*?)#/", $title, $aTag);
		
		if(!empty($aTag[1]))
		{
		    foreach ($aTag[1] as $v){
		
		        $this->state->child("tag")->createChild()
		        ->setData("title",trim($v));
		    }
		}
		
		
        try{
            
			$this->state->save(true);
        }catch (ExecuteException $e)
        {
            if(!$e->isDuplicate())
            {
                throw $e ;
            }
            else 
            {
            }
        }
        
        return $this->state->stid;
	}
	
	/**
	 * 分离正文和链接
	 * @param string $sBody 微博(用户状态)正文
	 * @return array 第一个元素是处理后的正文,其他是分离出来的链接
	 */
	public function getUrls($sBody){
		$sBody = (string)$sBody;
		preg_match_all('/(http|https):\/\/\S+(\s|$)/', $sBody ,$arrMatches, PREG_OFFSET_CAPTURE);
		if(!$arrMatches){
			return array($sBody,null);
		}
		//反向删除正文中的url
		$arrMatchesEnd = array_reverse($arrMatches[0]);
		$arrUrls = array();
		foreach($arrMatchesEnd as $arrMatche){
			$sBody = str_replace($arrMatche[0], '', $sBody);
			array_unshift($arrUrls, trim($arrMatche[0]));
		}
		return array($sBody,$arrUrls);
	}
	/**
	 * 获取多个url的媒体类型
	 * @param array $arrUrls url数组
	 * @return array 结果数组,url为键,类型为值
	 */
	function getUrlType(array $arrUrls){
		//迭代的判断url的媒体类型,直到全部搞定
		do{
			$this->getUrlTypeIteration($arrUrls);
		}while(!$this->isAllUrlHasType($arrUrls));
		//整理结果
		return $arrUrls;
	}
	/**
	 * 检查url数组是否都已经判定了类型
	 * @return bool
	 */
	function isAllUrlHasType(array & $arrUrls){
		foreach($arrUrls as $arrUrl){
			if($arrUrl['type'] === ''){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 转换成opencomb关心的媒体类型
	 * @param string $sType html媒体类型
	 */
	function getDisplayType($sType){
		$arrImg = array(
				'application/x-bmp',
				'image/gif',
				'application/x-ico',
				'image/jpeg',
				"image/png",
				);
		$arrSWF = array(
				'application/x-shockwave-flash',
				'application/vnd.adobe.workflow',
				);
		$arrVideo = array(
				'video/avi',
				'video/mpeg4',
				'video/mpg',
				"video/x-ms-wmv",
				"video/x-ms-wmv",
				"application/vnd.rn-realmedia",//rm
				"application/vnd.rn-realmedia-vbr",//rmvb
				);
		$arrAudio = array(
				'audio/mp3',
				"audio/wav",
				"audio/x-ms-wma",
				);
		
		$arrType = explode(' ', $sType);
		$sType = $arrType[0];
		
		if(array_search($sType ,$arrImg) !==false){
			return 'image';
		}elseif(array_search($sType ,$arrSWF) !==false){
			return 'application/x-shockwave-flash';
		}elseif(array_search($sType ,$arrVideo) !==false){
			return 'video';
		}elseif(array_search($sType ,$arrAudio) !==false){
			return 'audio';
		}else{
			return 'text/html';
		}
	}
	
	/**
	 *
	 */
	function getUrlTypeIteration(array & $arrUrls){
		$arrSockets = array ();
		//组装socket
		foreach ( $arrUrls as $sKey => $arrUrl ) {
			if($arrUrl['type']!==''){
				continue;
			}
			$arrUrlPart = parse_url ( $arrUrl['url'] );
			$address = $arrUrlPart ['host'];
			$query = @$arrUrlPart ['query'] ? '?'.$arrUrlPart ['query'] : '';
			$fragment = @$arrUrlPart ['fragment'] ? $arrUrlPart ['fragment'] : '';
			$path = @$arrUrlPart ['path'] ? $arrUrlPart ['path'] : '/';
			$path .=  $query . $fragment;
			$service_port = @$arrUrlPart ['port'] ? $arrUrlPart ['port'] : 80;
			$socket = fsockopen ( $address, $service_port );
			
			fwrite ( $socket, "GET " . $path . " HTTP/1.1\r\n" );
			fwrite ( $socket, "Host:" . $address . "\r\n" );
			fwrite ( $socket, "Connection:keep-alive\r\n" );
			fwrite ( $socket, "Cache-Control:max-age=0\r\n" );
			//单独为tudou准备了cookie
			fwrite ( $socket, "Cookie:juid=016mpolu851dvv; tudouad_citycode=210000; testjuid=true; seid=016ms3gb2f2653; seidtimeout=1330237386119; pageStep=3; pageUUID=3522b8a3-1e81-4151-a3e3-1f0032c18f9a~_~136; playedRecord=20jeml%2C1zsu0p%2C20e9db%2C; playTimer_0=1330235586070%7C16682%7C121837773; apyuid=\"000000bzE7Kn06GC83GCtR,6KX0GCtR\"\r\n" );
			// 		fwrite ( $socket, "Pragma: no-cache\r\n" );
			fwrite ( $socket, "User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11\r\n" );
			fwrite ( $socket, "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" );
			fwrite ( $socket, "Accept-Encoding:gzip,deflate,sdch\r\n" );
			fwrite ( $socket, "Accept-Language:zh-CN,zh;q=0.8\r\n" );
			fwrite ( $socket, "Accept-Charset:UTF-8,*;q=0.5\r\n" );
			fwrite ( $socket, "\r\n" );
	
			$arrSockets [$sKey] = $socket;
		}
		$arrResults = array ();
		$emptyArray = array ();
	
		$arrReadySockets = $arrSockets;
		$selectResult = stream_select ( $arrReadySockets, $emptyArray, $emptyArray, 3 );
		if ($selectResult === false) {
			exit ();
		}else if($selectResult === 0){
			foreach($arrUrls as & $arrUrl){
				$arrUrl['type'] = false;
				return;
			}
		}
	
		foreach ( $arrReadySockets as $nKeyForSocket => $hSocket ) {
			$sSocketIdx = array_search ( $hSocket, $arrSockets );
				
			if (feof ( $hSocket )) {
				continue;
			}
				
			$arrHeaderInfo = array ();
				
			while ( ! feof ( $hSocket ) ) {
				// echo "reading socket \r\n" ;
	
				$sResponseHeaderLine = fgets ( $hSocket, 1024 );
				// 			print_r( $sResponseHeaderLine);
				// 遇到head结尾,不读后面的body了
				if ($sResponseHeaderLine == "\r\n") {
					unset ( $arrSockets [$sSocketIdx] );
					@fclose ( $hSocket );
					break;
				}
					
				if(strpos ( $sResponseHeaderLine, 'HTTP/' ) === 0 || strpos ( $sResponseHeaderLine, 'http/' ) === 0){
					$arrHeaderInfo ['state_code'] = $sResponseHeaderLine;
				}else{
					@list ( $sHeaderName, $sHeaderValue ) = explode ( ":", $sResponseHeaderLine, 2 );
					$sHeaderName = trim ( $sHeaderName );
					$sHeaderName = strtolower ( $sHeaderName );
					$arrHeaderInfo [$sHeaderName] = trim ( $sHeaderValue );
				}
			}
				
			// 如果找到302转发,就重新组织url列表
			if (strpos($arrHeaderInfo ['state_code'],'302') !== false) {
				unset ( $arrSockets [$sSocketIdx] );
				@fclose ( $hSocket );
				$arrUrls[$sSocketIdx]['type'] = '';
				$arrUrls[$sSocketIdx]['url'] = $arrHeaderInfo['location'];
// 				$arrUrls[$arrHeaderInfo['location']] = $arrUrls[$sSocketIdx];
// 				unset($arrUrls[$sSocketIdx]);
				continue;
			}
			
			// 如果找到404
			if (strpos($arrHeaderInfo ['state_code'],'404') !== false) {
				unset ( $arrSockets [$sSocketIdx] );
				@fclose ( $hSocket );
				$arrUrls[$sSocketIdx]['type'] = false;
				continue;
			}
				
			if (isset($arrHeaderInfo['content-type'])) {
				$sHeaderValue = trim ( $arrHeaderInfo['content-type'] );
				$arrUrls[$sSocketIdx]['type'] = $sHeaderValue;
				unset ( $arrSockets [$sSocketIdx] );
				@fclose ( $hSocket );
				continue;
			}else{
				$arrUrls[$sSocketIdx]['type'] = false;
			}
		}
	}
}

?>