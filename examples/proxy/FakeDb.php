<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 20/02/2016
 * Time: 22:57
 */

namespace MacFJA\PhpKVO\examples\proxy;


class FakeDb
{
    protected $sql;

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    public function query($sqlRequest)
    {
        $this->sql = $sqlRequest;
        echo 'Do SQL request'.PHP_EOL;
    }
}