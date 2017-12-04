<?php
namespace Models;

class Stage extends \Models\ModelBase
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
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $location_lat;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $location_lng;

    /**
     *
     * @var string
     * @Column(type="string", length=70, nullable=false)
     */
    public $image;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("Festmapp");
        $this->setSource("Stage");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Stage';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Stage[]|Stage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Stage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
