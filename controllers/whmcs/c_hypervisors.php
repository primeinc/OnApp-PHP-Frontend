<?php
class Hypervisors
{
    private $factory_instance;

    private function get_factory() {
        if ( !isset($this->factory_instance) ) {
            require_once "wrapper/Factory.php";

            $this->factory_instance = new ONAPP_Factory(
                $_SESSION["host"],
                $_SESSION["login"],
                onapp_cryptData($_SESSION["password"], 'decrypt')
            );
       }
       return $this->factory_instance;
    }

    public function view()
    {
        $id = onapp_get_arg('id');
        $action = onapp_get_arg('action');

        switch ( $action ) {
            case 'hypervisor_details':
                $this->vm_details( $id );
                break;
            case 'reboot':
                $this->reboot( $id );
                break;
            default:
                $this->show_template_view();
                break;
        }
    }

    private function show_template_view()
    {
        onapp_debug(__CLASS__.' :: '.__FUNCTION__);

        onapp_permission(array('hypervisors', 'hypervisors.read'));

        $onapp = $this->get_factory();

        $hypervisor = $onapp->factory('Hypervisor');
        $hypervisor_obj = $hypervisor->getList( );

        $hypervisor_zones = $onapp->factory('HypervisorZone');
        $hypervisor_zones_obj = $hypervisor_zones->getList( );

        $vm = $onapp->factory('VirtualMachine');
        $vm_obj = $vm->getList();

        $hypervisor_vm_count = $this->get_vms_quantity($hypervisor_obj, $vm_obj);

        $hypervisor_xm_info = $this->get_hv_xm_info($hypervisor_obj);

        $params = array(
            'hypervisor_obj'        =>     $hypervisor_obj,
            'hypervisor_vm_count'   =>     $hypervisor_vm_count,
            'hypervisor_zones_obj'  =>     $hypervisor_zones_obj,
            'hypervisor_xm_info'    =>     $hypervisor_xm_info,
            'vm_obj'                =>     $vm_obj,
            'info_title'            => onapp_string('HYPERVISORS_'),
            'info_body'             => onapp_string('HYPERVISORS_INFO'),
        );
        onapp_show_template( 'hypervisor_view', $params );
    }

    private function get_vms_quantity($hypervisor_obj, $vm_obj)
    {
        foreach($hypervisor_obj as $hv)
        {
            $quantity = 0;
            foreach($vm_obj as $vm)
                if($vm->_hypervisor_id == $hv->_id)
                    $quantity++;
            $hypervisor_vms_quatity[$hv->_id] = $quantity;
        }
        return $hypervisor_vms_quatity;
    }

    private function get_hv_xm_info($hypervisor_obj)
    {
        foreach($hypervisor_obj as $key => $value)
        {
            $xm = explode("\n", $value->_health->xm_info);
            foreach($xm as $key1 => $value1)
            {
                $result = explode(':',$value1);
                $hypervisor_xm_info[$value->_id][trim($result[0])] = trim($result[1]);
            }
        }
        return $hypervisor_xm_info;
    }
       // Don't delete this function please
    /*private function get_hv_xm_info($hypervisor_obj) {
        foreach ( $hypervisor_obj as $hv_id => $hv ) {
            foreach ( $hv->_health as $label => $info ) {
                $xm = explode("\n", $info);
                foreach ( $xm as $key => $value ) {
                    $result = explode( ':', $value);
                    $hypervisor_xm_info['hypervisors'][$hv->_id][$label][trim($result[0])] = trim($result[1]);
                }
            }
        }
        return $hypervisor_xm_info;
    }*/

    /**
     * Reboots Hypervisor
     *
     * @global array $_ALIASES menu page aliases
     * @param integer $id hypervisor id
     */
    private function reboot ( $id ) {
        global $_ALIASES;

        onapp_debug( __METHOD__ );

        onapp_debug('id => ' . $id);

        onapp_permission( array( 'hypervisors_reboot', 'hypervisors') );

        $onapp = $this->get_factory();

        $hypervisor = $onapp->factory('Hypervisor', ONAPP_WRAPPER_LOG_REPORT_ENABLE);
        $hypervisor->reboot( $id );
        onapp_debug('hypervisor_obj => ' . print_r($hypervisor, true));

        if (is_null($hypervisor->error)) {
            onapp_event_exec( 'hypervisor_reboot', array( $hypervisor->_obj ) );
            $_SESSION['message'] = 'HYPERVISOR_WILL_BE_REBOOTED_SHORTLY';
            onapp_redirect( ONAPP_BASE_URL . '/' . $_ALIASES['hypervisors'] );
        }
        else {
            onapp_event_exec( 'hypervisor_reboot_failed', array( $hypervisor->_obj ) );
            trigger_error ( print_r( $hypervisor->error, true ) );
            $this->show_template_view( $hypervisor->error );
        }
    }

    /**
    *
    * Checks necessary access to this class
    *
    * @return boolean [true|false]
    *
    */
    static function  access(){
       return onapp_has_permission(array('hypervisors', 'hypervisors.read'));
    }
}