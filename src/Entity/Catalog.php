<?php

namespace App\Entity;

use App\Service\Functions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Runner\Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CatalogRepository")
 */
class Catalog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Chapter", mappedBy="myCatalog", orphanRemoval=true)
     */
    private $myChapters;

    private $myCirculationsNU;
    public $dirPath;

    public function __construct()
    {
        $this->myChapters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Chapter[]
     */
    public function getMyChapters(): Collection
    {
        return $this->myChapters;
    }

    public function addMyChapter(Chapter $myChapter): self
    {
        if (!$this->myChapters->contains($myChapter)) {
            $this->myChapters[] = $myChapter;
            $myChapter->setMyCatalog($this);
        }

        return $this;
    }

    public function removeMyChapter(Chapter $myChapter): self
    {
        if ($this->myChapters->contains($myChapter)) {
            $this->myChapters->removeElement($myChapter);
            // set the owning side to null (unless already changed)
            if ($myChapter->getMyCatalog() === $this) {
                $myChapter->setMyCatalog(null);
            }
        }

        return $this;
    }

    public function getMyCirculationsNU()
    {
        return $this->myCirculationsNU;
    }

    /**
     * @return  self
     */ 
    public function setMyCirculationsNU($myCirculationsNU)
    {
        $this->myCirculationsNU = $myCirculationsNU;

        return $this;
    }

    public function ReadFromDir($dirName,$readLevel = false)
    {
        if (substr($dirName,-1,1) == '/') $dirName = rtrim($dirName,"/");
        $this->dirPath = $dirName;
        $catBaseName = baseName($dirName);
        
        $chapterFile = @fopen($dirName.'/'.$catBaseName.'.D^D','r');
        if (!$chapterFile) {
            $chapterFile = Functions::FindFileByDirNameAndOpen($dirName,'D^D');
        }
        $firstRow = explode('$',fgets($chapterFile));
        $this->name = trim($firstRow[0]);
        $myChapters = array();
        while($line = fgets($chapterFile)){
            $chapter = new Chapter;
            $chapter->setMyCatalog($this);
            $chapter->ReadFrom($line,$readLevel);
            //czasem są np. dwa rozdziały 2
            //ArrayCollection nie obsługuje array_key_exists
            $key = Functions::AppendixForDuplicateKeys($chapter->getName(),$myChapters);
            $myChapters[$key] = 1;
            $this->myChapters[$key] = $chapter;
        }
        fclose($chapterFile);
        //nakłady ogólne wczytać

    }
    public static function LoadFrom($pathDir,$readLevel = false)
    {
        if (! is_dir($pathDir)) return;
        if (substr($pathDir,-1,1) == '/') $pathDir = rtrim($pathDir,"/");
        $dir = opendir($pathDir);
        $catalogs = array();
        $key = 0;
        while($catDir = readdir($dir))
        {
            if($catDir == '.' || $catDir == '..')continue;
            $catDir = $pathDir.'/'.$catDir;
            if(!is_dir($catDir) ) continue;
            $catalog = new Catalog;
            if(CATALOG_DIST & $readLevel){
                $catalog->ReadFromDir($catDir,$readLevel);
                $key = Functions::AppendixForDuplicateKeys($catalog->getName(),$catalogs);
            } 
            // $catalogs[] = $catalog;
            $catalogs[$key] = $catalog;
            if (!$readLevel) $key++; 
        }
        closedir($dir);
        return $catalogs;
    }

}
