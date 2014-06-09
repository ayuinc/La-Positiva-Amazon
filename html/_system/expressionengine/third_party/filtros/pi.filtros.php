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
    'pi_name'         => 'Filtros',
    'pi_version'      => '1.0',
    'pi_author'       => 'Gianfranco Montoya',
    'pi_author_url'   => 'http://www.ayuinc.com/',
    'pi_description'  => 'Allows states qualify Friends',
    'pi_usage'        => Filtros::usage()
);
            
class Filtros 
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

            {exp:filtros}

        This is an incredibly simple Plugin.
            <?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    // END

    public function brand(){
        $form = '<select name="marca" id="marca" > <option value="MARCA" selected>MARCA</option>';
        ee()->db->distinct('marca');
        ee()->db->select('marca');
        $query = ee()->db->get('exp_valor_autos');
        foreach($query->result() as $row){
            $aux=$row->marca;
            $aux= str_replace(" ", "-",$aux);
            $form .= '<option value='.$aux.'>'.$row->marca.'</option>';
        }
        $form = $form.'</select>';
        return $form;
    }

    public function model(){
        $form = '<option value="MODELO" selected>MODELO</option>';
        $marca = ee()->TMPL->fetch_param('marca');
        $marca = str_replace("-", " ",$marca);
        ee()->db->distinct('modelo');
        ee()->db->select('modelo');
        ee()->db->where('marca',$marca);
        $query = ee()->db->get('exp_valor_autos');
        foreach($query->result() as $row){
            $aux=$row->modelo;
            $aux= str_replace(" ", "-",$aux);
            $form .= '<option value='.$aux.'>'.$row->modelo.'</option>';
        }
        return $form;
    }

    public function version(){
        $form = '<option value="VERSION" selected>VERSIÓN</option>';
        $modelo = ee()->TMPL->fetch_param('modelo');
        $marca = ee()->TMPL->fetch_param('marca');
        $marca = str_replace("-", " ",$marca);
        ee()->db->distinct('version');
        ee()->db->select('version');
        ee()->db->where('modelo',$modelo);
        ee()->db->where('marca',$marca);
        $query = ee()->db->get('exp_valor_autos');
        foreach($query->result() as $row){
            $aux=$row->version;
            $aux= str_replace(" ", "-",$aux);
            $form .= '<option value='.$aux.'>'.$row->version.'</option>';
        }
        return $form;
    } 

    public function year(){
        $form = '<option value="AÑO" selected>AÑO</option>';
        $aux = '1998';
        $version = ee()->TMPL->fetch_param('version');
        $modelo = ee()->TMPL->fetch_param('modelo');
        $marca = ee()->TMPL->fetch_param('marca');
        $marca = str_replace("-", " ",$marca);
        $modelo = str_replace("-", " ",$modelo);
        ee()->db->select('*');
        ee()->db->where('version', $version);
        ee()->db->where('modelo',$modelo);
        ee()->db->where('marca',$marca);
        $query = ee()->db->get('exp_valor_autos');
        foreach($query->result() as $row){
            if ($row->ano_1998!='') {
                $form .= '<option value='.$row->ano_1998.'>1998</option>';
            }
            if ($row->ano_1999!='') {
                $form .= '<option value='.$row->ano_1999.'>1999</option>';
            }
            if ($row->ano_2000!='') {
                $form .= '<option value='.$row->ano_2000.'>2000</option>';
            }
            if ($row->ano_2001!='') {
                $form .= '<option value='.$row->ano_2001.'>2001</option>';
            }
            if ($row->ano_2002!='') {
                $form .= '<option value='.$row->ano_2002.'>2002</option>';
            }
            if ($row->ano_2003!='') {
                $form .= '<option value='.$row->ano_2003.'>2003</option>';
            }
            if ($row->ano_2004!='') {
                $form .= '<option value='.$row->ano_2004.'>2004</option>';
            }
            if ($row->ano_2005!='') {
                $form .= '<option value='.$row->ano_2005.'>2005</option>';
            }
            if ($row->ano_2006!='') {
                $form .= '<option value='.$row->ano_2006.'>2006</option>';
            }
            if ($row->ano_2007!='') {
                $form .= '<option value='.$row->ano_2007.'>2007</option>';
            }
            if ($row->ano_2008!='') {
                $form .= '<option value='.$row->ano_2008.'>2008</option>';
            }
            if ($row->ano_2009!='') {
                $form .= '<option value='.$row->ano_2009.'>2009</option>';
            }
            if ($row->ano_2010!='') {
                $form .= '<option value='.$row->ano_2010.'>2010</option>';
            }
            if ($row->ano_2011!='') {
                $form .= '<option value='.$row->ano_2011.'>2011</option>';
            }
            if ($row->ano_2012!='') {
                $form .= '<option value='.$row->ano_2012.'>2012</option>';
            }
            if ($row->ano_2013!='') {
                $form .= '<option value='.$row->ano_2013.'>2013</option>';
            }

        }
        return $form;
    }

    public function region(){
        $form = '<select name="region" id="region"> <option value="REGION" selected>DEPARTAMENTO</option>';
        ee()->db->distinct('departamento');
        ee()->db->select('departamento');
        $query = ee()->db->get('exp_agencias');
        foreach($query->result() as $row){
            $aux=$row->departamento;
            $aux= str_replace(" ", "-",$aux);
            $form .= '<option value='.$aux.'>'.$row->departamento.'</option>';
        }
        $form = $form.'</select>';
        return $form;

    }

    public function district(){
        $form = '<option value="DISTRITO" selected>CIUDAD/DISTRITO</option>';
        $region = ee()->TMPL->fetch_param('region');
        ee()->db->distinct('distrito');
        ee()->db->select('distrito');
        ee()->db->where('departamento',$region);
        $query = ee()->db->get('exp_agencias');
        foreach($query->result() as $row){
            $aux=$row->distrito;
            $aux= str_replace(" ", "-",$aux);
            $form .= '<option value='.$aux.'>'.$row->distrito.'</option>';
        }
        return $form;

    }

    public function agency(){
        $div = '';
        $district = ee()->TMPL->fetch_param('distrito');
        ee()->db->select('*');
        ee()->db->where('distrito',$district);
        $query = ee()->db->get('exp_agencias');
        foreach($query->result() as $row){
            $div .='<div class="result">';
            $div .= '<p>'.$row->oficina.'</p>';
            $div .= '<p>'.$row->direccion.'</p>';
            $div .= '<p>'.$row->ubicacion.'</p>';
            $div .= '</div>';
        }
        return $div;


    }
} 
/* End of file pi.rating.php */
/* Location: ./system/expressionengine/third_party/rating/pi.rating.php */