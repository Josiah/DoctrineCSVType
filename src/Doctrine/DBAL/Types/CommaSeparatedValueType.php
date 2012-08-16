<?php namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Type that maps a set of comma separated values to a numerically indexed php
 * array.
 *
 * @author Josiah <josiah@jjs.id.au>
 */
class CommaSeparatedValueType extends TextType
{
    /**
     * Name supplied to doctrine for this type
     *
     * @var string
     */
    const TYPE_NAME = 'csv';

    /**
     * Delimiter to use for separating values
     *
     * @var string
     */
    const DELIMITER = ',';

    /**
     * Enclosure to use for separating values
     *
     * @var string
     */
    const ENCLOSURE = '"';

    /**
     * Escape prefix to use for enclosures within value strings
     *
     * @var string
     */
    const ESCAPE = '\\';

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return $value;
        } else {
            $values = array();
            foreach ($value as $raw) {
                if (!$raw) {
                    $values[] = static::ENCLOSURE.static::ENCLOSURE;
                } else if (false !== strpos($raw, static::DELIMITER)
                    || false !== strpos($raw, static::ENCLOSURE)) {
                    $escaped = static::ENCLOSURE;
                    $escaped .= str_replace(
                        array(static::ESCAPE, static::ENCLOSURE),
                        array(
                            static::ESCAPE.static::ESCAPE,
                            static::ENCLOSURE.static::ENCLOSURE
                        ),
                        $raw);
                    $escaped .= static::ENCLOSURE;

                    $values[] = $escaped;
                } else {
                    $values[] = $raw;
                }
            }

            return implode(static::DELIMITER, $values);
        }
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        } if (strlen($value) === 0) {
            return array();
        } else {
            return str_getcsv($value, static::DELIMITER, static::ENCLOSURE, static::ESCAPE);
        }
    }

    /** @override */
    public function getName()
    {
        return TYPE_NAME;
    }
}

Type::addType(CommaSeparatedValueType::TYPE_NAME, 'Doctrine\\DBAL\\Types\\CommaSeparatedValueType');
