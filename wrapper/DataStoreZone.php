<?PHP

/**
 * Data Store Zone
 *
 * @todo Add description
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2011 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/ONAPP.php';

/**
 *
 * Managing Network Zones
 *
 * The ONAPP_DataStoreZone class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * The ONAPP_DataStoreZone class represents virtual machine data store groups.
 * The ONAPP class is a parent of ONAPP_DataStoreZone class.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/data_store_zones.xml</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/data_store_zones/{ID}.xml</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/data_store_zones.xml</i>
 *
 * <data_store_groups type="array">
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <data_store_groups type="array">
 *  <data_store_group>
 *    <label>{LABEL}</label>
 *  </data_store_group>
 * </data_store_groups>
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/data_store_zones/{ID}.xml</i>
 *
 * <?xml version="1.0" encoding="UTF-8"?>
 * <data_store_groups type="array">
 *  <data_store_group>
 *    <label>{LABEL}</label>
 *  </data_store_group>
 * </data_store_groups>
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/data_store_zones/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/data_store_zones.json</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/data_store_zones/{ID}.json</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/data_store_zones.json</i>
 *
 * <code>
 * {
 *      data_store_group: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/data_store_zones/{ID}.json</i>
 *
 * <code>
 * {
 *      data_store_group: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/data_store_zones/{ID}.json</i>
 *
 *
 *
 */

class ONAPP_DataStoreZone extends ONAPP {

    /**
     * the Hypervisor's Zone ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Data Store Zone Label
     *
     * @var integer
     */
    var $_label;

    /**
     * the Data Store Zone creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the Data Store Zone update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'data_store_group';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'data_store_zones';

    /**
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_DataStoreZone';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'data_store_group';
        }

        if( is_null( $version ) ) {
            $version = $this->_version;
        }

        switch( $version ) {
            case '2.0':
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                 
                );

                break;

            case '2.1':

                $this->_fields = $this->_init_fields('2.0');

                break;
        }

        return $this->_fields;
    }

        function getResource( $action = ONAPP_GETRESOURCE_DEFAULT ) {
            return parent::getResource( $action );

            /**
             * ROUTE :
             * @name user_data_store_groups
             * @method GET
             * @alias  /data_store_zones(.:format)
             * @format {:controller=>"data_store_groups", :action=>"index"}
             */

            /**
             * ROUTE :
             * @name user_data_store_group
             * @method GET
             * @alias  /data_store_zones/:id(.:format)
             * @format  {:controller=>"data_store_groups", :action=>"show"}
             */

            /**
             * ROUTE :
             * @name
             * @method POST
             * @alias  /data_store_zones(.:format)
             * @format  {:controller=>"data_store_groups", :action=>"create"}
             */

            /**
             * ROUTE :
             * @name
             * @method PUT
             * @alias  /data_store_zones/:id(.:format)
             * @format {:controller=>"data_store_groups", :action=>"update"}
             */

            /**
             * ROUTE :
             * @name
             * @method DELETE
             * @alias  /data_store_zones/:id(.:format)
             * @format {:controller=>"data_store_groups", :action=>"destroy"}
             */

        }

}

?>
