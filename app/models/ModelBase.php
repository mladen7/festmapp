<?php
namespace Models;

use Phalcon\Mvc\Model;
use Phalcon\Exception;

class ModelBase extends Model
{

    public function hasAttribute($attribute)
    {
        return $this->getModelsMetaData()->hasAttribute($this, $attribute);
    }

    //basic JSON to Model method for one to one relation
    public function promoteSimple($json)
    {
        foreach ($json as $key => $value) {

            if (is_object($value)) {
                //TO DO: add foreach for one to many
                $object = new $key();
                $object->promoteSimple($value);
                $this->{$key} = $object;
            } else {
                $this->{$key} = $value;
            }
        }
    }

    //JSON to Model method
    public function promote($std_class, $target_class = null)
    {
        try {

            /*
             * << Establish the parent Model type >>
             * Allow the user to specify the target_class by providing a string or object.
             * Assume extending class when null.
             */
            if (is_null($target_class)) {
                $object = $this;
            } elseif (is_object($target_class)) {
                $object = $target_class;
            } elseif (is_string($target_class)) {
                $modelName = "Models\\$target_class";
                $object = new $modelName();  //New up a Model from a user-supplied class name--usually during recursion
            } else {
                return $std_class;  //Not playing nice?  Return the data passed in.
            }

            /*
             * Prepare this array in case we encounter an array of related records nested in our stdClass
             * Each element will be an array of one or more...the children being instances of the related Model (related to the parent).
             */

            $related_entities = array();  //eventual array of arrays

            /* Loop through the stdClass, accessing properties like an array. */
            foreach ($std_class as $property => $value) {
                /*
                 * If its object, should be evaluated as one to one relationship
                 */
                if (is_object($value)) {
                    //TO DO: add foreach for one to many
                    $modelName = "Models\\$property";
                    $relatedObject = new $modelName();
                    $relatedObject->promote($value);
                    $object->{$property} = $relatedObject;
                } /*
                 * If an array is found as the value of a property we assume it is full of realted entities;
                 * with the property name being the Model type (case sensitive)
                 *
                 */
                elseif (is_array($value)) {  //all of these are stdClass as well, so we recurse to handle each one
                    /*
                     * $property should be named to fit the model of the entities in the array
                     * This is dependent on the user building the JSON object correctly upstream.
                     *
                     */
                    $related_entities[$property] = array();

                    foreach ($value as $entity) {  //Get each array element and treat it as an entity
                        /*
                         * For thought-simplicity sake, let's assume this promote() call doesn't find related entities inside this related entity (Yo Dawg...).
                         * This adds the related entity to an array named for its Model: $related_entities['related_model_name'] = $object_returned_from_promote().
                         * This WILL, of course, recurse to infinity building out the complete data model.
                         */
                        array_push($related_entities[$property], $this->promote($entity, $property));
                        // $related_entities[$property] = $this->promote($entity, $property);
                    }

                } else {
                    /* Just add the value found to the property of the Model object */
                    //validate does attribute on model exists
                    if ($object->hasAttribute($property))
                        $object->{$property} = $value;
                    else {
                        //throw new \Exception($property);
                        throw new Exceptions\InvalidRESTParameterException(400, "Bad Request", $property);

                    }

                    //throw an exception, if not
                }
            }

            /*
             * Add each array of whatever related entities were found, to the parent object/table
             * This depends on the Phalcon ORM Model convention: $MyTableObject->relatedSomthings = array_of_related_somethings
             */
            foreach ($related_entities as $related_model => $entity_data) {
                $object->{$related_model} = $entity_data;
            }

        } catch (\Exception $e) {
            /*
             * If the user supplied data (decoded JSON) that does not match the Model we are going to experience an exception
             * when trying to access a property that doesn't exist.
             *
             */
            throw $e;
        }
        return $object; /* Usually only important when we are using recursion. */
    }


}