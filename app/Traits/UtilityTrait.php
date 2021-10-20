<?php
/**
 * User: jovishin
 * Date: 2017/5/10
 * Time: 上午3:04
 */

namespace App\Traits;


use DB;
use Exception;
use Schema;

trait UtilityTrait {

    /**
     * @return array All fields list.
     * @throws Exception Error message.
     */
    protected function getAllFields () {
        if ( !empty( self::getFillable() ) ) {
            return self::getFillable();
        }
        if ( empty( self::getGuarded() ) || self::getGuarded()[0] === '*' ) {
            throw new Exception( 'fillable or guarded is not set.' );
        } else {
            $columns = Schema::getColumnListing( self::getTable() );

            return array_values( array_diff( $columns, self::getGuarded() ) );
        }
    }

    /**
     * get enum options of table column
     *
     * @param string $column
     * @param string $table default = null
     *
     * @return array
     */
    protected function getEnumOptions ( $column, $table = null ) {
//        $table = new static;
//        $table = $table->getTable();
        if ( empty( $table ) ) {
            $table = self::getTable();
        }
        $type = DB::select( DB::raw( "SHOW COLUMNS FROM " . $table . " WHERE Field = '" . $column . "'" ) )[0]->Type;
        preg_match( '/^enum\((.*)\)$/', $type, $matches );
        $enum = [];
        foreach ( explode( ',', $matches[1] ) as $value ) {
            $v      = trim( $value, "'" );
            $enum[] = $v;
        }

        return $enum;
    }

}
