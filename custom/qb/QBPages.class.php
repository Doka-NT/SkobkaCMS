<?php
class QBPages {
   
    public static function Page(){
        $oQuery = QB::Load(Path::Arg(1));
        if(!$oQuery->qbid)
            return Menu::NotFound();
        Theme::SetTitle($oQuery->name);
        $prefix = QB::ControlLinks($oQuery);
        
        return $prefix . QB::View($oQuery);
    }
}
