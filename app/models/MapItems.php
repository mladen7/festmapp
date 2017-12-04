<?php
namespace Models;

class MapItems extends \Models\ModelBase
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     */
    public $type;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    public $location_lat;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    public $location_lng;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $marker_icon;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("Festmapp");
        $this->setSource("MapItems");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'MapItems';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MapItems[]|MapItems
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MapItems
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
