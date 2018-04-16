<?php
    require_once(dirname(__FILE__).'/../config/api.php');

    /*
     * return string
     * ex. 'localhost:8081' || ''( while error)
     */
    function get_one_active_url(){
        $api_list = api_list();
        shuffle($api_list);
        $valid_api_url = '';

        for($i = 0; $i < count($api_list); $i++){
            $cur_api_url = $api_list[$i];
            $cur_api_port = substr($cur_api_url, strpos($cur_api_url,":") + 1);
			
            if(isActive($cur_api_url)){
                $valid_api_url = $cur_api_url;
                break;
            }else{
                reStart($cur_api_port);
            }
        }

        if($valid_api_url == ''){return [0, 'no active url'];}
        return [1, $valid_api_url];
    }

    /*
     * Isactive check by giving url;
     * 
     * $api_url comes from api_list()
     * timeout: 200ms
     */
    function isActive($api_url){
        if(curl_http_status('http://'.$api_url.'/isactive', 4) == 200) {
            return true;
        }else{
            return false;
        }
    }

    /*
     * Restart service
     * using : 
     *
     * filename: <port>.bat
     * test url: http://localhost:9091/restart?port=8081&filename=8081.bat
     */
    function reStart($target_port, $monitoring_url = ''){
        if($monitoring_url == ''){$monitoring_url = default_monitoring_url();}

        if(curl_http_status('http://'.$monitoring_url.'/restart?port='.$target_port.'&filename='.$target_port.'.bat', 4) == 200) {
            return true;
        }else{
            return false;
        }
    }

    function curl_http_status($url, $timeout = 0.5, $rettype = CURLINFO_HTTP_CODE){
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_NOSIGNAL, true);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, $timeout*100);
        curl_exec($handle);
        $info = curl_getinfo($handle, $rettype);
        curl_close($handle);
        return $info;
    }

    function curl_http($url, &$data, $timeout = 5){
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_NOSIGNAL, true);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, $timeout*100);
        curl_exec($handle);
        if(curl_getinfo($handle, CURLINFO_HTTP_CODE)==200){
            $data = curl_multi_getcontent($handle);
            curl_close($handle);
            return true;
        }else{
            curl_close($handle);
            return false;
        }
    }
?>