<?php

namespace MacFJA\PhpKVO;

/**
 * Class AbstractObservable.
 *
 * Abstract implementation of `MacFJA\PhpKVO\Observable` interface.
 * It contains all needed method to do KVC et KVO.
 *
 * @package MacFJA\PhpKVO
 * @author  MacFJA
 * @license MIT
 */
abstract class AbstractObservable implements Observable
{
    use ObservableTrait;
}
