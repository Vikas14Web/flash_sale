<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\Common\Collections\Criteria;

class Filter
{
    public const INT_EQUALS = '=';
    public const STRING_EQUALS = 'equals';
    public const BOOL_EQUALS = 'is';
    public const NOT_EQUALS = 'neq';
    public const CONTAINS = 'contains';
    public const STARTS_WITH = 'startsWith';
    public const ENDS_WITH = 'endsWith';
    public const IN_ARRAY = 'in';
    public const GREATER_THAN = '>';
    public const GREATER_THAN_EQUALS = '>=';
    public const LESS_THAN = '<';
    public const LESS_THAN_EQUALS = '<=';
    public const DATE_BETWEEN = 'db';
    public const DATE_GREATER_THAN = 'after';
    public const DATE_GREATER_THAN_EQUALS = 'onOrAfter';
    public const DATE_LESS_THAN = 'before';
    public const DATE_LESS_THAN_EQUALS = 'onOrBefore';

    private static array $filterMethodMapping = [
        self::INT_EQUALS => 'equals',
        self::STRING_EQUALS => 'equals',
        self::BOOL_EQUALS => 'booleanEquals',
        self::NOT_EQUALS => 'notEquals',
        self::CONTAINS => 'contains',
        self::STARTS_WITH => 'startsWith',
        self::ENDS_WITH => 'endsWith',
        self::IN_ARRAY => 'inArray',
        self::GREATER_THAN => 'greaterThan',
        self::GREATER_THAN_EQUALS => 'greaterThanEquals',
        self::LESS_THAN => 'lessThan',
        self::LESS_THAN_EQUALS => 'lessThanEquals',
        self::DATE_BETWEEN => 'dateBetween',
        self::DATE_GREATER_THAN => 'dateGreaterThan',
        self::DATE_GREATER_THAN_EQUALS => 'dateGreaterThanEquals',
        self::DATE_LESS_THAN => 'dateLessThan',
        self::DATE_LESS_THAN_EQUALS => 'dateLessThanEquals',
    ];

    public static function apply($filters): Criteria
    {
        $criteria = new Criteria();
        foreach ($filters as $filter) {
            [$field, $operator, $value] = array_values((array) $filter);
            $method = self::$filterMethodMapping[$operator];
            self::$method($criteria, $field, $value);
        }

        return $criteria;
    }

    public static function find(array $filters, string $fieldName, string $operator = self::STRING_EQUALS): string|int|array|null
    {
        foreach ($filters as [$fld, $op, $value]) {
            if ($fld === $fieldName && $op === $operator) {
                return $value;
            }
        }

        return null;
    }

    private static function booleanEquals($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->eq($field, filter_var($value, \FILTER_VALIDATE_BOOLEAN)));
    }

    private static function equals($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->eq($field, $value));
    }

    private static function notEquals($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->neq($field, $value));
    }

    private static function contains($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->contains($field, $value));
    }

    private static function startsWith($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->startsWith($field, $value));
    }

    private static function endsWith($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->endsWith($field, $value));
    }

    private static function inArray($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->in($field, $value));
    }

    private static function greaterThan($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->gt($field, $value));
    }

    private static function greaterThanEquals($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->gte($field, $value));
    }

    private static function lessThan($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->lt($field, $value));
    }

    private static function lessThanEquals($criteria, $field, $value): void
    {
        $criteria->andWhere(Criteria::expr()->lte($field, $value));
    }

    private static function dateBetween($criteria, $field, $value): void
    {
        $startDateFormattedValue = date('Y-m-d H:i:s', strtotime($value[0]));
        $endDateFormattedValue = date('Y-m-d H:i:s', strtotime($value[1]));
        $criteria->andWhere(
            Criteria::expr()->orX(
                Criteria::expr()->gte($field, $startDateFormattedValue),
                Criteria::expr()->lte($field, $endDateFormattedValue),
            )
        );
    }

    private static function dateEquals($criteria, $field, $value): void
    {
        $formattedValue = date('Y-m-d H:i:s', strtotime($value));
        $criteria->andWhere(Criteria::expr()->eq($field, $formattedValue));
    }

    private static function dateGreaterThan($criteria, $field, $value): void
    {
        $formattedValue = date('Y-m-d H:i:s', strtotime($value));
        $criteria->andWhere(Criteria::expr()->gt($field, $formattedValue));
    }

    private static function dateGreaterThanEquals($criteria, $field, $value): void
    {
        $formattedValue = date('Y-m-d H:i:s', strtotime($value));
        $criteria->andWhere(Criteria::expr()->gte($field, $formattedValue));
    }

    private static function dateLessThan($criteria, $field, $value): void
    {
        $formattedValue = date('Y-m-d H:i:s', strtotime($value));
        $criteria->andWhere(Criteria::expr()->lt($field, $formattedValue));
    }

    private static function dateLessThanEquals($criteria, $field, $value): void
    {
        $formattedValue = date('Y-m-d H:i:s', strtotime($value));
        $criteria->andWhere(Criteria::expr()->lte($field, $formattedValue));
    }
}
