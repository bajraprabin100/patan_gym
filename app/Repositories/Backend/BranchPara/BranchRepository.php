<?php

namespace App\Repositories\Backend\BranchPara;
use App\Repositories\Backend\BaseAbstract;
use App\Repositories\Backend\BaseInterface;

/**
 * Class BranchRepository.
 *
 * @author Kourtier
 */
class BranchRepository extends  BaseAbstract implements BranchParaInterface
{
    function model()
    {
        return 'App\Models\Admin\BranchPara';
    }


}