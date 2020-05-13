<?php

namespace App\Entity\Circulation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Equipment_N_U extends CirculationNameAndUnit
{
    public function GenerateQueryToInsert()
    {
        return parent::GenerateQueryToInsert()."'equipment_n_u'),";
    }
    public function AddSelfToCorrectSubArray(array &$separatedArray)
    {
        $separatedArray['S'][]=$this ;
    }
}