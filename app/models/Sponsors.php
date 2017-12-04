<?php
namespace Models;

class Sponsors extends \Models\ModelBase
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
     * @Column(type="string", length=50, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=70, nullable=false)
     */
    public $logoImage;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $linkToWebsite;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkToSocial;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("Festmapp");
        $this->setSource("Sponsors");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Sponsors';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sponsors[]|Sponsors
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sponsors
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
