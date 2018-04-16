<?php
    /*
     * Setting of uri & port;
     * ex. ['localhost:8081', 'localhost:8082', '202.112.195.192:8083']
     */
    function api_list(){
        return [
            'localhost:8081',
            'localhost:8082',
            'localhost:8083',
            'localhost:8084',
            'localhost:8085',
            'localhost:8086',
            'localhost:8087',
            'localhost:8088',
            'localhost:8089',
            'localhost:8090',
            'localhost:8091',
            'localhost:8092',
            'localhost:8093',
            'localhost:8094',
            'localhost:8095',
            'localhost:8096',
            'localhost:8097',
            'localhost:8098',
            'localhost:8099',
            'localhost:8100',
        ];
    }

    /*
     * Setting of monitor uri & port;
     * ex. 'localhost:9091'
     */
    function default_monitoring_url(){
        return 'localhost:9091';
    }

    function restarting_file(){
        return 'AUTO-GENERATED-DO-NOT-EDIT.hz';
        //return 'AUTO-GENERATED-DO-NOT-EDIT';
    }
?>
