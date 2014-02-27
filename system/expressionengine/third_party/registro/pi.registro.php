<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Memberlist Class
 *
 * @package     ExpressionEngine
 * @category        Plugin
 * @author      Gianfranco Montoya 
 * @copyright       Copyright (c) 2014, Gianfranco Montoya
 * @link        http://www.ayuinc.com/
 */

$plugin_info = array(
    'pi_name'         => 'Registro',
    'pi_version'      => '1.0',
    'pi_author'       => 'Gianfranco Montoya',
    'pi_author_url'   => 'http://www.ayuinc.com/',
    'pi_description'  => 'Allows states qualify Friends',
    'pi_usage'        => Registro::usage()
);
            
class Registro
{

    var $return_data = "";
    // --------------------------------------------------------------------

        /**
         * Memberlist
         *
         * This function returns a list of members
         *
         * @access  public
         * @return  string
         */
    public function __construct(){
        $this->EE =& get_instance();
        $var = $this->EE->input->post('count');
    }

    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * This function describes how the plugin is used.
     *
     * @access  public
     * @return  string
     */
    public static function usage()
    {
        ob_start();  ?>
        The Memberlist Plugin simply outputs a
        list of 15 members of your site.

            {exp:registro}

        This is an incredibly simple Plugin.
            <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    // END

    public function registrar_cliente(){
        $marca=ee()->TMPL->fetch_param('marca');
        $modelo=ee()->TMPL->fetch_param('modelo');
        $version=ee()->TMPL->fetch_param('version');
        $precio=ee()->TMPL->fetch_param('precio');
        $ano_auto=ee()->TMPL->fetch_param('ano');
        $res_ci=ee()->TMPL->fetch_param('res_ci');
        $perd_robo=ee()->TMPL->fetch_param('perd_robo');
        $perd_choque=ee()->TMPL->fetch_param('perd_choque');
        $da_propios=ee()->TMPL->fetch_param('da_propios');

        $marca = str_replace("-", " ", $marca);
        $modelo = str_replace("-", " ", $modelo);
        $version = str_replace("-", " ", $version);
        $ano='ano_'.$ano_auto;
        ee()->db->select('id');
        ee()->db->where('marca',$marca);
        ee()->db->where('modelo',$modelo);
        ee()->db->where('version',$version);
        ee()->db->where($ano,$precio);
        $auto_query = ee()->db->get('exp_valor_autos');
        foreach($auto_query->result() as $row){
            $auto=$row->id;
        }
        if($res_ci=='No Contratado'){
            $res_ci='0';
        }
        else{
            switch ($res_ci) {
                case 15:
                    $res_ci='50';
                    break;
                case 20:
                    $res_ci='100';
                    break;
                case 25:
                    $res_ci='150';
                    break;
                case 30:
                    $res_ci='200';
                    break;
                case 35:
                    $res_ci='250';
                    break;
                case 40:
                    $res_ci='300';
                    break;
            }
        }
        if($perd_robo=='No Contratado'){
            $perd_robo='0';
        }else{
            $perd_robo='1';
        }
        if($perd_choque=='No Contratado'){
            $perd_choque='0';
        }else{
            $perd_choque='1';
        }
        if($da_propios=='No Contratado'){
            $da_propios='0';
        }else{
            $da_propios='1';
        }
        ee()->db->select('id');
        ee()->db->where('res_ci',$res_ci);
        ee()->db->where('perd_robo',$perd_robo);
        ee()->db->where('perd_choque',$perd_choque);
        ee()->db->where('da_propios',$da_propios);
        $seguro_query = ee()->db->get('exp_seguros');
        foreach($seguro_query->result() as $row){
            $seguro=$row->id;
        }
        $data = array(
            'dni'=>ee()->TMPL->fetch_param('dni') ,
            'name'=>ee()->TMPL->fetch_param('name') ,
            'surname'=>ee()->TMPL->fetch_param('surname') ,
            'email'=>ee()->TMPL->fetch_param('email'),
            'phone'=>ee()->TMPL->fetch_param('phone'),
            'ano_auto' => $ano_auto,
            'auto_id'=>$auto,
            'seguro_id'=>$seguro
            );
        if(ee()->db->insert('exp_clientes_seguros', $data)){
            return '<p class="thanks-msg">TU COTIZACIÓN HA SIDO ENVIADA A:</p>';
        }
        else{
            return "";
        }
    }
} 
/* End of file pi.registro.php */
/* Location: ./system/expressionengine/third_party/registro/pi.registro.php */