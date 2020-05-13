<?php

namespace App\Entity\Circulation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Labor_N_U extends CirculationNameAndUnit
{
    public function GenerateQueryToInsert()
    {
        return parent::GenerateQueryToInsert()."'labor_n_u'),";
    } 
    public function AddSelfToCorrectSubArray(array &$separatedArray)
    {
        $separatedArray['R'][]=$this ;
    }
}