<?php
/**
 * @author  cow
 * 畅卓短信
 * @dataTime 2017.03.21
 */
	namespace mobileCode;

	class ChangZhuoSend {
		private	$userid= ""; //用户ID
		private $account= "7723youxi"; //用户名
		private $password= "cn7723"; //密码
		private $target = "http://sms.chanzor.com:8001/sms.aspx";//地址

        /**
         *发送验证码
         * @param $mobile
         * @param $msg
         * @return string
         */
		public function send($mobile,$msg){
			$post_data = "action=send&userid=&account=".$this->account."&password=".$this->password."&mobile=".$mobile."&sendTime=&content=".rawurlencode($msg);
			$gets = $this->Post($post_data, $this->target);
			//var_dump($gets);
			$start=strpos($gets,"<?xml");
			$data=substr($gets,$start);
			$xml=simplexml_load_string($data);
			//var_dump(json_decode(json_encode($xml),TRUE));
			$return=json_decode(json_encode($xml),TRUE);
			if($return['returnstatus']=='Success'){
				return 'success';
			}else{
				return $return['message'];
			}
		}

        /**
         * 数据传输
         * @param $data
         * @param $target
         * @return string
         */
		protected function Post($data, $target) {
			$url_info = parse_url($target);
			$httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
			$httpheader .= "Host:" . $url_info['host'] . "\r\n";
			$httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
			$httpheader .= "Content-Length:" . strlen($data) . "\r\n";
			$httpheader .= "Connection:close\r\n\r\n";
			//$httpheader .= "Connection:Keep-Alive\r\n\r\n";
			$httpheader .= $data;
			$fd = fsockopen($url_info['host'], 80);
			fwrite($fd, $httpheader);
			$gets = "";
			while(!feof($fd)) {
				$gets .= fread($fd, 128);
			}
			fclose($fd);
			return $gets;
		}

        /**
         * 短信模板生成
         * @param int $type
         * @param array $param
         * @return string
         */
		public function getMessageContent($type=1,array $param=array()){
            $code = isset($param['code'])?$param['code']:$this->makeMobileCode(6);
            $msg = '';
            switch ($type){
                case 1:
                    $msg = "您本次的登录验证码是:{$code}有效时间5分钟.【7723游戏】";
                    break;
            }
            return $msg;
        }

        /**
         * 生成随机验证码
         * @param int $number
         * @return string
         */
        public function makeMobileCode($number = 6) {
            $result = '';
            $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
            for ($i = 1; $i <= $number; $i++) {
                $rand_num = rand(0, 9);
                $result .= $array[$rand_num];
            }
            return $result;
        }
	}	
?>