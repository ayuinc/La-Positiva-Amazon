<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Memberlist Class
 *
 * @package     ExpressionEngine
 * @category    Plugin
 * @author      Herman Marin
 * @copyright   Copyright (c) 2013, Herman Marin
 * @link        http://tucirculo.net/insertarcrm/
 */

$plugin_info = array(
    'pi_name'         => 'Insertar CRM',
    'pi_version'      => '1.0',
    'pi_author'       => 'Herman Marin',
    'pi_author_url'   => 'http://tucirculo.net/',
    'pi_description'  => 'Inserta la información de los clientes que cotizan seguros en la positiva en el CRM',
    'pi_usage'        => InsertarCRM::usage()
);

class InsertarCRM
{

    public $return_data = "";

    // --------------------------------------------------------------------

    /**
     * InsertarCRM
     *
     * This function returns a list of members
     *
     * @access  public
     * @return  string
     */
    public function __construct()
    {
        $query = ee()->db->select('screen_name')
            ->get('members', 15);

        foreach($query->result_array() as $row)
        {
            $this->return_data .= $row['screen_name'];
            $this->return_data .= "<br />";
        }
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

    {exp:insertarcrm}

This is an incredibly simple Plugin.


    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
    // END
}
/* End of file pi.insertarcrm.php */
/* Location: ./system/expressionengine/third_party/insertarcrm/pi.insertarcrm.php */