<?php

namespace andreev1024\templater\components;

/**
 * Config class for Templater module.
 */
class Config
{
    const TYPE_PDF = 'pdf';
    const TYPE_MAIL = 'mail';
    const TYPE_NOTIFICATION = 'notification';

    const ENTITY_BUY_ORDER_CONTRACT = 'buyOrderContract';
    const ENTITY_SELL_ORDER_CONTRACT = 'sellOrderContract';
    const ENTITY_INVOICE = 'invoice';
    const ENTITY_MAIL = 'mail';
    const ENTITY_NOTIFICATION = 'notification';

    /**
     * Returns type by entity.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $entity
     * @param null $default
     *
     * @return null
     */
    public static function getTypeByEntity($entity, $default = null)
    {
        $entities = static::getEntities();
        return isset($entities[$entity]['type']) ?
            $entities[$entity]['type'] :
            $default;
    }

    /**
     * Returns entity by type.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $type
     *
     * @return array
     */
    public static function getEntityByType($type)
    {
        $entities = [];
        foreach (static::getEntities() as $entityName => $entity) {
            if ($entity['type'] === $type) {
                $entities[$entityName] = $entity;
            }
        }
        return $entities;
    }

    /**
     * Returns entities with all related data.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getEntities()
    {
        return [
            static::ENTITY_BUY_ORDER_CONTRACT => [
                'class' => '\modules\buyOrder\models\BuyOrderContract',
                'type' => static::TYPE_PDF,
            ],
            static::ENTITY_SELL_ORDER_CONTRACT => [
                'class' => '\modules\sellOrder\models\SellOrderContract',
                'type' => static::TYPE_PDF,
            ],
            static::ENTITY_INVOICE => [
                'class' => '\modules\cashflow\models\Invoice',
                'type' => static::TYPE_PDF,
            ],
            static::ENTITY_MAIL => [
                'class' => '\modules\mailer\models\Email',
                'type' => static::TYPE_MAIL,
            ],
            static::ENTITY_NOTIFICATION => [
                'class' => '\modules\notification\models\Notification',
                'type' => static::TYPE_NOTIFICATION,
            ],
        ];
    }

    /**
     * Returns an entity.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $entity
     *
     * @return mixed
     */
    public static function getEntity($entity)
    {
        return static::getEntities()[$entity];
    }

    /**
     * Returns entity by class name.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $className
     *
     * @return null
     */
    public static function getEntityByClassName($className)
    {
        $className = '\\' . trim($className, '\\');
        foreach (static::getEntities() as $entityName => $entity) {
            if ($entity['class'] === $className) {
                return $entityName;
            }
        }
        return null;
    }

    /**
     * Returns all types.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_NOTIFICATION,
            static::TYPE_PDF,
            static::TYPE_MAIL,
        ];
    }
}
