<?php

namespace App\Entity\Circulation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Material_N_U extends CirculationNameAndUnit
{
    public function GenerateQueryToInsert()
    {
        return parent::GenerateQueryToInsert()."'material_n_u'),";
    }
    public function AddSelfToCorrectSubArray(array &$separatedArray)
    {
        $separatedArray['M'][]=$this ;
    }
}