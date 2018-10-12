<?php

class PHPJasperXMLElement extends abstractPHPJasperXML
{
    private $defaultfont='helvetica';
    private $defaultfontsize = 10;
	public function __construct()
	{

	}



	public function element_staticText($data,$elementid) 
    {

        $this->elementid=$elementid;
        $align="L";
        $fill=0;
        $border=0;
        $fontsize=$this->defaultfontsize;
        $font=$this->defaultfont;
        $fontstyle="";
        $textcolor = array("r"=>0,"g"=>0,"b"=>0);
        $fillcolor = array("r"=>255,"g"=>255,"b"=>255);
        $txt="";
        $rotation="";
        $drawcolor=array("r"=>0,"g"=>0,"b"=>0);
        $height=$data->reportElement["height"];
        $stretchoverflow="true";
        $printoverflow="false";
        $data->hyperlinkReferenceExpression=$this->analyse_expression($data->hyperlinkReferenceExpression);
        $data->hyperlinkReferenceExpression=trim(str_replace(array(" ",'"'),"",$data->hyperlinkReferenceExpression));
        if(isset($data->reportElement["forecolor"])) {
            
            $textcolor = array('forecolor'=>$data->reportElement["forecolor"],"r"=>hexdec(substr($data->reportElement["forecolor"],1,2)),"g"=>hexdec(substr($data->reportElement["forecolor"],3,2)),"b"=>hexdec(substr($data->reportElement["forecolor"],5,2)));
        }
        if(isset($data->reportElement["backcolor"])) {
            $fillcolor = array('backcolor'=>$data->reportElement["backcolor"],"r"=>hexdec(substr($data->reportElement["backcolor"],1,2)),"g"=>hexdec(substr($data->reportElement["backcolor"],3,2)),"b"=>hexdec(substr($data->reportElement["backcolor"],5,2)));
        }
        if($data->reportElement["mode"]=="Opaque") {
            $fill=1;
        }
        if(isset($data["isStretchWithOverflow"])&&$data["isStretchWithOverflow"]=="true") {
            $stretchoverflow="true";
        }
        if(isset($data->reportElement["isPrintWhenDetailOverflows"])&&$data->reportElement["isPrintWhenDetailOverflows"]=="true") {
            $printoverflow="true";
            $stretchoverflow="false";
        }
        if(isset($data->box)) 
        {           
            $border=$this->drawBorder($data);        
        }
        if(isset($data->textElement["textAlignment"])) {
            $align=$this->get_first_value($data->textElement["textAlignment"]);
        }
        if(isset($data->textElement["verticalAlignment"])) 
        {
            $valign="T";
            if($data->textElement["verticalAlignment"]=="Bottom")
                $valign="B";
            elseif($data->textElement["verticalAlignment"]=="Middle")
                $valign="C";
            else
                $valign="T";

        }
        if(isset($data->textElement["rotation"])) {
            $rotation=$data->textElement["rotation"];
        }
        if(isset($data->textElement->font["fontName"])) {
          
            $font=$this->recommendFont($data->text,$data->textElement->font["fontName"],$data->textElement->font["pdfFontName"]);
                
        }
        if(isset($data->textElement->font["size"])) {
            $fontsize=$data->textElement->font["size"];
        }
        if(isset($data->textElement->font["isBold"])&&$data->textElement->font["isBold"]=="true") {
            $fontstyle=$fontstyle."B";
        }
        if(isset($data->textElement->font["isItalic"])&&$data->textElement->font["isItalic"]=="true") {
            $fontstyle=$fontstyle."I";
        }
        if(isset($data->textElement->font["isUnderline"])&&$data->textElement->font["isUnderline"]=="true") {
            $fontstyle=$fontstyle."U";
        }
        if(isset($data->reportElement["key"])) {
            $height=$fontsize*$this->adjust;
        }
        $mydata=[];
        $mydata[]=array(
            "type"=>"SetXY",
            "x"=>(int)$data->reportElement["x"],
            "y"=>(int)$data->reportElement["y"],
            "hidden_type"=>"SetXY",
            "elementid"=>$this->elementid
        );
        $mydata[]=array(
            "type"=>"SetTextColor",
            'forecolor'=>(string)$data->reportElement["forecolor"],
            "r"=>$textcolor["r"],
            "g"=>$textcolor["g"],
            "b"=>$textcolor["b"],
            "hidden_type"=>"textcolor",
            "elementid"=>$this->elementid
        );
        $mydata[]=array(
            "type"=>"SetDrawColor",
            "r"=>$drawcolor["r"],
            "g"=>$drawcolor["g"],
            "b"=>$drawcolor["b"],
            "hidden_type"=>"drawcolor",
            "elementid"=>$this->elementid
        );
        $mydata[]=array(
            "type"=>"SetFillColor",
            'backcolor'=>(string)$data->reportElement["backcolor"],
            "r"=>$fillcolor["r"],
            "g"=>$fillcolor["g"],
            "b"=>$fillcolor["b"],
            "hidden_type"=>"fillcolor",
            "elementid"=>$this->elementid
        );
        $mydata[]=array(
            "type"=>"SetFont",
            "font"=>$font,
            "pdfFontName"=>$data->textElement->font["pdfFontName"],
            "fontstyle"=>$fontstyle,
            "fontsize"=>$fontsize,
            "hidden_type"=>"font",
            "elementid"=>$this->elementid
        );
                
        //### UTF-8 characters, a must for me.  
        $txtEnc=$data->text; 
                
        $mydata[] = array(
            "type"=>"MultiCell",
            "width"=>$data->reportElement["width"],
            "height"=>$height,
            "uuid"=>$data->reportElement['uuid'],
            "txt"=>$txtEnc,"border"=>$border,
            "align"=>$align,
            "fill"=>$fill,
            "hidden_type"=>"statictext",
            "soverflow"=>$stretchoverflow,
            "poverflow"=>$printoverflow,
            "rotation"=>$rotation,
            "valign"=>$valign,
            "x"=>(int)$data->reportElement["x"],
            "y"=>(int)$data->reportElement["y"],
            "elementid"=>$this->elementid);
        return $mydata;

    }

    
   public function element_image($data,$elementid) 
   {
        $mydata=[];
         $this->elementid=$elementid;
         $imagepath=$data->imageExpression;
       
         switch($data['scaleImage']) 
         {
            case "FillFrame":
                $scaleType = "FillFrame";
                break;
            case "RetainShape":
                $scaleType = "RetainShape";
                break;
            case "Clip":
                $scaleType = "Clip";
                break;
            case "RealHeight":
                $scaleType = "RealHeight";
                break;
            case "RealSize":
                $scaleType = "RealSize";
                break;
            default:
                $scaleType = ""; 
                break;
        }
        $mydata[]= [
            "type"=>"Image",
            "path"=>$imagepath,
            "x"=>(int)$data->reportElement["x"],
            "y"=>(int)$data->reportElement["y"],
            "width"=>(int)$data->reportElement["width"],
            "height"=>(int)$data->reportElement["height"],
            "imgtype"=>$imagetype,
            "link"=>$data->hyperlinkReferenceExpression,
            "uuid"=>$data->reportElement['uuid'], 
            "hidden_type"=>"image",
            "linktarget"=>(string)$data["hyperlinkTarget"],
            "elementid"=>$this->elementid, 
            "scale_type" => $scaleType
        ];
        return $mydata;

    }




    public function element_componentElement($data) {

        $x=$data->reportElement["x"];
        $y=$data->reportElement["y"];
        $width=$data->reportElement["width"];
        $height=$data->reportElement["height"];
       foreach($data->children('jr',true) as $barcodetype =>$content)
       {           
           
           $barcodemethod="";
           $textposition="";
            if($barcodetype=="barbecue"){
                $barcodemethod=$data->children('jr',true)->attributes('', true) ->type;
                $textposition="";
                $checksum=$data->children('jr',true)->attributes('', true) ->checksumRequired;
                $code=$content->codeExpression;
                if($content->attributes('', true) ->drawText=='true')
                {
                        $textposition="bottom";
                }
                
                $modulewidth=$content->attributes('', true) ->moduleWidth;
                
            }else{
                
                $barcodemethod=$barcodetype;
                $textposition=$content->attributes('', true)->textPosition;       
                $code=$content->codeExpression;
                $modulewidth=$content->attributes('', true)->moduleWidth;
                 

                
            }

            if($modulewidth=="")
            {
                $modulewidth=0.4;
            }


            return array("type"=>"Barcode","barcodetype"=>$barcodemethod,"x"=>$x,"y"=>$y,"width"=>$width,"height"=>$height,'textposition'=>$textposition,'code'=>$code,'modulewidth'=>$modulewidth,"elementid"=>$this->elementid,"uuid"=>$data->reportElement['uuid'],);

           
       }
       
    }

    
    public function element_break($data) 
    {
        return array("type"=>"break","hidden_type"=>"break","elementid"=>$this->elementid);                
    }

    public function element_crossTab($data,$elementid){
        $mydata=[];
        $this->elementid=$elementid;
        //var_dump($data);die;
        $x=$data->reportElement['x']+0;
        $y=$data->reportElement['w']+0;
        $ctwidth=$data->reportElement['width']+0;
        $height=$data->reportElement['height']+0;
        $dataset=$data->crosstabDataset->dataset->datasetRun['subDataset']."";
        
        $rowgroup=array();
        
        
        
        foreach($data->rowGroup as $r =>$rd)
        {                  
            $rowheaderalign=$rd->crosstabRowHeader->cellContents->textField->textElement['textAlignment']."";
            if($rowheaderalign=="")
                    $rowheaderalign="center";
            
            $rowheadervalign=$rd->crosstabRowHeader->cellContents->textField->textElement['verticalAlignment']."";
           
            if($rd->crosstabRowHeader->cellContents['mode'].""=="Opaque")
            $rowheaderbgcolor=$rd->crosstabRowHeader->cellContents['backcolor']."";
             $rowheaderisbold=$rd->cellContents->textField->textElement->font['isBold']."";
            $rowexpression=$rd->bucket->bucketExpression;
            $rowgroupfield=$rd->crosstabRowHeader->cellContents->textField->textFieldExpression;
            $style=array("width"=>$rd["width"]+0,
                    'rowheaderbgcolor'=>$rowheaderbgcolor,"rowheaderalign"=>$rowheaderalign,"rowheadervalign"=>$rowheadervalign,
                    "rowheaderisbold"=>$rowheaderisbold);
            $rowgroup[]=array("name"=>$rd['name']."","field"=>$rowgroupfield."","style"=>$style);
            
        }
        
        foreach($data->columnGroup as $c =>$cd){
            $colheaderalign=$cd->crosstabColumnHeader->cellContents->textField->textElement['textAlignment']."";
            if($colheaderalign=="")$colheaderalign="center";
            $colheadervalign=$cd->crosstabColumnHeader->cellContents->textField->textElement['verticalAlignment']."";
            if($cd->crosstabColumnHeader->cellContents['mode'].""=="Opaque")
            $colheaderbgcolor=$cd->crosstabColumnHeader->cellContents['backcolor']."";
             $colheaderisbold=$cd->crosstabColumnHeader->cellContents->textField->textElement->font['isBold']."";
             $height=$cd['height']+0;
             
          $colgroupfield=$cd->crosstabColumnHeader->cellContents->textField->textFieldExpression;
          $style=array("colheaderalign"=>$colheaderalign,"colheadervalign"=>$colheadervalign,"colheaderbgcolor"=>$colheaderbgcolor,
                "colheaderisbold"=>$colheaderisbold,"height"=>$height);
        
            $colgroup[]=array("name"=>$cd['name']."","field"=>$colgroupfield."","style"=>$style);
            
        }
        $measuremethod=$data->measure['calculation']."";
        $measurefield=$data->measure->measureExpression."";
        
        
        $crosstabcell=array();
        $i=0;
         foreach($data->crosstabCell as $ce =>$cecontent){

            $ceheaderalign=$cecontent->cellContents->textField->textElement['textAlignment']."";
            $ceheadervalign=$cecontent->cellContents->textField->textElement['verticalAlignment']."";
            if($cecontent->cellContents['mode'].""=="Opaque")
             $ceheaderbgcolor=$cecontent->cellContents['backcolor']."";
             $ceheaderisbold=$cecontent->cellContents->textField->textElement->font['isBold']."";
             $width=$cecontent['width']+0;
             $style=array("ceheaderalign"=>$ceheaderalign,"ceheadervalign"=>$ceheadervalign,"ceheaderbgcolor"=>$ceheaderbgcolor,
                 "ceheaderisbold"=>$ceheaderisbold,"width"=>$width);

            $crosstabcell[]=array("no"=>$i,"style"=>$style);
            $i++;
            
        }
        
           $mydata[]=array("type"=>"CrossTab","x"=>$x,"y"=>$y,"width"=>$ctwidth,"height"=>$height,"dataset"=>$dataset,
               'rowgroup'=>$rowgroup,'colgroup'=>$colgroup,'measuremethod'=>$measuremethod,'measurefield'=>$measurefield,'crosstabcell'=>$crosstabcell,"elementid"=>$this->elementid);

        return $mydata;

        
    }
    
    public function element_line($data,$elementid){  
        $mydata=[];
        $this->elementid=$elementid;
        $drawcolor=array("r"=>0,"g"=>0,"b"=>0);
        $hidden_type="line";
         if($data->graphicElement->pen["lineWidth"]>0)
         {
            $linewidth=$data->graphicElement->pen["lineWidth"];
         }
            
         if($linewidth=="")
            $linewidth=0.5;
     
       
            if(isset($data->graphicElement->pen["lineStyle"])) {
                if($data->graphicElement->pen["lineStyle"]=="Dotted")
                {
                    $dash="0,1";
                }
                elseif($data->graphicElement->pen["lineStyle"]=="Dashed")
                {
                    $dash="4,2"; 
                }
                else
                {
                    $dash="";
                }
                //Dotted Dashed
            }
           
            
          
        if(isset($data->reportElement["forecolor"])) {
            $drawcolor=array("r"=>hexdec(substr($data->reportElement["forecolor"],1,2)),"g"=>hexdec(substr($data->reportElement["forecolor"],3,2)),"b"=>hexdec(substr($data->reportElement["forecolor"],5,2)));
        }

        if(isset($data->reportElement["positionType"])&&$data->reportElement["positionType"]=="FixRelativeToBottom") {
            $hidden_type="relativebottomline";
        }
        
        $style=array('color'=>$drawcolor,'width'=>$linewidth,'dash'=>$dash);

        
        if($data->reportElement["width"][0]+0 > $data->reportElement["height"][0]+0)    //width > height means horizontal line
        {
            $mydata[]=array("type"=>"Line", "x1"=>$data->reportElement["x"]+0,"y1"=>$data->reportElement["y"]+0,
                "x2"=>$data->reportElement["x"]+$data->reportElement["width"],"y2"=>$data->reportElement["y"]+$data->reportElement["height"]-1,
                "hidden_type"=>$hidden_type,"style"=>$style,"forecolor"=>$data->reportElement["forecolor"]."","printWhenExpression"=>$data->reportElement->printWhenExpression,"elementid"=>$this->elementid);
        }
        elseif($data->reportElement["height"][0]+0>$data->reportElement["width"][0]+0)      //vertical line
        {
            $mydata[]=array("type"=>"Line", "x1"=>$data->reportElement["x"],"y1"=>$data->reportElement["y"],
                "x2"=>$data->reportElement["x"]+$data->reportElement["width"]-1,"y2"=>$data->reportElement["y"]+$data->reportElement["height"],"hidden_type"=>$hidden_type,"style"=>$style,
                "forecolor"=>$data->reportElement["forecolor"]."","printWhenExpression"=>$data->reportElement->printWhenExpression,"elementid"=>$this->elementid);
        }
        
        
        $mydata[]=array("type"=>"SetDrawColor","r"=>0,"g"=>0,"b"=>0,"hidden_type"=>"drawcolor","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetFillColor","r"=>255,"g"=>255,"b"=>255,"hidden_type"=>"fillcolor","elementid"=>$this->elementid);
        return $mydata;
    }

    public function element_rectangle($data,$elementid)
    {
        $mydata=[];
        $this->elementid=$elementid;
        $radius=$data['radius']+0;
                $mode=$data->reportElement["mode"]."";
        $drawcolor=array("r"=>0,"g"=>0,"b"=>0);

        $borderwidth=1;
           
           if(isset($data->graphicElement->pen["lineWidth"]))
                 $borderwidth=$data->graphicElement->pen["lineWidth"];
            
             if(isset($data->graphicElement->pen["lineColor"]))
                 $drawcolor=array("r"=>hexdec(substr($data->graphicElement->pen["lineColor"],1,2)),"g"=>hexdec(substr($data->graphicElement->pen["lineColor"],3,2)),"b"=>hexdec(substr($data->graphicElement->pen["lineColor"],5,2)));
            
            $dash="";
                    if($data->graphicElement->pen["lineStyle"]=="Dotted")
                    $dash="0,1";
                elseif($data->graphicElement->pen["lineStyle"]=="Dashed")
                    $dash="4,2"; 
                elseif($data->graphicElement->pen["lineStyle"]=="Solid")
                    $dash="";

            if(isset($data->reportElement["forecolor"])) {
            $drawcolor=array("r"=>hexdec(substr($data->reportElement["forecolor"],1,2)),"g"=>hexdec(substr($data->reportElement["forecolor"],3,2)),"b"=>hexdec(substr($data->reportElement["forecolor"],5,2)));         
        } 
            $border=array("LTRB" => array('width' => $borderwidth+0,'color' =>$drawcolor,'cap'=>'square',
                            'join'=>'miter','dash'=>$dash));
                    
        if(isset($data->reportElement["backcolor"])  && ($mode=='Opaque'|| $mode=='')) { 
           
            $fillcolor=array("r"=>hexdec(substr($data->reportElement["backcolor"],1,2)),"g"=>hexdec(substr($data->reportElement["backcolor"],3,2)),"b"=>hexdec(substr($data->reportElement["backcolor"],5,2)));         
        }
        else
               $fillcolor=array("r"=>255,"g"=>255,"b"=>255);

        $printWhenExpression= $data->reportElement->printWhenExpression."";
        $mydata[]=array("type"=>"RoundedRect","x"=>$data->reportElement["x"]+0,
                "y"=>$data->reportElement["y"]+0,"width"=>$data->reportElement["width"]+0,
            "height"=>$data->reportElement["height"]+0,"hidden_type"=>"roundedrect","radius"=>$radius,
                "fillcolor"=>$fillcolor,
                "mode"=>$mode,
                'border'=>array("LRTB"=>$border),
                "elementid"=>$this->elementid,"printWhenExpression"=>$printWhenExpression);

        return $mydata;
    }

  public function element_ellipse($data,$elementid) 
  {
        $this->elementid=$elementid;
        $mydata=[];
        $drawcolor=array("r"=>0,"g"=>0,"b"=>0);
        $fillcolor=array("r"=>255,"g"=>255,"b"=>255);
         $width=1;
           
            
                
           if(isset($data->graphicElement->pen["lineWidth"]))
                 $borderwidth=$data->graphicElement->pen["lineWidth"];
            
             if(isset($data->graphicElement->pen["lineColor"]))
                 $drawcolor=array("r"=>hexdec(substr($data->graphicElement->pen["lineColor"],1,2)),"g"=>hexdec(substr($data->graphicElement->pen["lineColor"],3,2)),"b"=>hexdec(substr($data->graphicElement->pen["lineColor"],5,2)));
            
            $dash="";
                    if($data->graphicElement->pen["lineStyle"]=="Dotted")
                    $dash="0,1";
                elseif($data->graphicElement->pen["lineStyle"]=="Dashed")
                    $dash="4,2"; 
                elseif($data->graphicElement->pen["lineStyle"]=="Solid")
                    $dash="";
//echo "$borderwidth,";
           
            $border=array("LTRB" => array('width' => $borderwidth,'color' =>$drawcolor,'cap'=>'square',
                            'join'=>'miter','dash'=>$dash));
           
        if(isset($data->reportElement["forecolor"])) {
            $drawcolor=array("r"=>hexdec(substr($data->reportElement["forecolor"],1,2)),"g"=>hexdec(substr($data->reportElement["forecolor"],3,2)),"b"=>hexdec(substr($data->reportElement["forecolor"],5,2)));         
        }
        if(isset($data->reportElement["backcolor"])) {
            $fillcolor=array("r"=>hexdec(substr($data->reportElement["backcolor"],1,2)),"g"=>hexdec(substr($data->reportElement["backcolor"],3,2)),"b"=>hexdec(substr($data->reportElement["backcolor"],5,2)));         
        }
        
        //$color=array("r"=>$drawcolor["r"],"g"=>$drawcolor["g"],"b"=>$drawcolor["b"]);
        $mydata[]=array("type"=>"SetFillColor","r"=>$fillcolor["r"],"g"=>$fillcolor["g"],"b"=>$fillcolor["b"],"hidden_type"=>"fillcolor","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetDrawColor","r"=>$drawcolor["r"],"g"=>$drawcolor["g"],"b"=>$drawcolor["b"],"hidden_type"=>"drawcolor","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"Ellipse","x"=>$data->reportElement["x"],"y"=>$data->reportElement["y"],"width"=>$data->reportElement["width"],"height"=>$data->reportElement["height"],"hidden_type"=>"ellipse","drawcolor"=>$drawcolor,"fillcolor"=>$fillcolor,'border'=>$border,"elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetDrawColor","r"=>0,"g"=>0,"b"=>0,"hidden_type"=>"drawcolor","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetFillColor","r"=>255,"g"=>255,"b"=>255,"hidden_type"=>"fillcolor","elementid"=>$this->elementid);

        return $mydata;
    }
    
    public function element_textField($data,$elementid) {
        $this->elementid=$elementid;
        $mydata=[];
        $align="L";
        $fill=0;
        $border=0;
        $fontsize=10;
        $font="helvetica";
        $rotation="";
        $fontstyle="";
        $textcolor = array("r"=>0,"g"=>0,"b"=>0);
        $fillcolor = array("r"=>255,"g"=>255,"b"=>255);
        $stretchoverflow="false";
        $printoverflow="false";
        $height=$data->reportElement["height"];
        $drawcolor=array("r"=>0,"g"=>0,"b"=>0);
        $data->hyperlinkReferenceExpression=$data->hyperlinkReferenceExpression;
        
        
        if(isset($data->reportElement["forecolor"])) {
            $textcolor = array("r"=>hexdec(substr($data->reportElement["forecolor"],1,2)),"g"=>hexdec(substr($data->reportElement["forecolor"],3,2)),"b"=>hexdec(substr($data->reportElement["forecolor"],5,2)));
        }
        if(isset($data->reportElement["backcolor"])) {
            $fillcolor = array("r"=>hexdec(substr($data->reportElement["backcolor"],1,2)),"g"=>hexdec(substr($data->reportElement["backcolor"],3,2)),"b"=>hexdec(substr($data->reportElement["backcolor"],5,2)));
        }
        if($data->reportElement["mode"]=="Opaque") {
            $fill=1;
        }
        if(isset($data["isStretchWithOverflow"])&&$data["isStretchWithOverflow"]=="true") {
            $stretchoverflow="true";
        }
        if(isset($data->reportElement["isPrintWhenDetailOverflows"])&&$data->reportElement["isPrintWhenDetailOverflows"]=="true") {
            $printoverflow="true";
        }
        if(isset($data->box)) {
          
           $border=$this->drawBorder($data);

        }
        if(isset($data->reportElement["key"])) {
            $height=$fontsize*$this->adjust;
        }
        if(isset($data->textElement["textAlignment"])) {
            $align=$this->get_first_value($data->textElement["textAlignment"]);
        }
        if(isset($data->textElement["verticalAlignment"])) {
            
            $valign="T";
            if($data->textElement["verticalAlignment"]=="Bottom")
                $valign="B";
            elseif($data->textElement["verticalAlignment"]=="Middle")
                $valign="C";
            else
                $valign="T";
            
            
        }
        if(isset($data->textElement["rotation"])) {
            $rotation=$data->textElement["rotation"];
        }
        if(isset($data->textElement->font["fontName"])) 
        {         
            $font=$data->textElement->font["fontName"];
        }
        if(isset($data->textElement->font["size"])) {
            $fontsize=$data->textElement->font["size"];
        }
        if(isset($data->textElement->font["isBold"])&&$data->textElement->font["isBold"]=="true") {
            $fontstyle=$fontstyle."B";
        }
        if(isset($data->textElement->font["isItalic"])&&$data->textElement->font["isItalic"]=="true") {
            $fontstyle=$fontstyle."I";
        }
        if(isset($data->textElement->font["isUnderline"])&&$data->textElement->font["isUnderline"]=="true") {
            $fontstyle=$fontstyle."U";
        }
        
        $mydata[]=array("type"=>"SetFont","font"=>$font."",
            "pdfFontName"=>$data->textElement->font["pdfFontName"]."","fontstyle"=>$fontstyle."","fontsize"=>$fontsize+0,"hidden_type"=>"font","elementid"=>$this->elementid);        
        $mydata[]=array("type"=>"SetXY","x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"hidden_type"=>"SetXY","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetTextColor","forecolor"=>$data->reportElement["forecolor"],"r"=>$textcolor["r"],"g"=>$textcolor["g"],"b"=>$textcolor["b"],"hidden_type"=>"textcolor","elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetFillColor","backcolor"=>$data->reportElement["backcolor"]."","r"=>$fillcolor["r"],"g"=>$fillcolor["g"],"b"=>$fillcolor["b"],"hidden_type"=>"fillcolor","fill"=>$fill,"elementid"=>$this->elementid);
        $mydata[]=array("type"=>"SetDrawColor","r"=>$drawcolor["r"],"g"=>$drawcolor["g"],"b"=>$drawcolor["b"],"hidden_type"=>"drawcolor","border"=>$border,"elementid"=>$this->elementid);        
         
        $data->reportElement['uuid']=$data->reportElement['uuid']."";

        switch ($data->textFieldExpression) {
            case 'new java.util.Date()':

                $mydata[]=array ("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>date("Y-m-d H:i:s"),"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"date","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>$data->hyperlinkReferenceExpression,"valign"=>$valign,
                  "uuid"=>$data->reportElement['uuid'],  "x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);

                break;
            case '"Page "+$V{PAGE_NUMBER}+" of"':
                $mydata[]=array("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>'Page $this->PageNo() of',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>$data->hyperlinkReferenceExpression,"pattern"=>$data["pattern"],"valign"=>$valign,
                   "uuid"=>$data->reportElement['uuid'], "x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);
                break;
            case '$V{PAGE_NUMBER}':
                
                // $this->pdf->getAliasNbPages();
                if(isset($data["evaluationTime"])&&$data["evaluationTime"]=="Report") {
                    $mydata[]=array("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>'{{:ptp:}}',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>$data->hyperlinkReferenceExpression,"pattern"=>$data["pattern"],"valign"=>$valign,
                      "uuid"=>$data->reportElement['uuid'],  "x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);
                }
                else {
                    $mydata[]=array("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>'$this->PageNo()',"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"pageno","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>$data->hyperlinkReferenceExpression,"pattern"=>$data["pattern"],"valign"=>$valign,
                        "uuid"=>$data->reportElement['uuid'],"x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);
                }
                break;
            case '" " + $V{PAGE_NUMBER}':
                $mydata[]=array("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>' {{:ptp:}}',
                    "border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"nb","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"link"=>$data->hyperlinkReferenceExpression,"pattern"=>$data["pattern"],"valign"=>$valign,
                    "uuid"=>$data->reportElement['uuid'],"x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);
                break;

            default:
                $writeHTML=false;
               
                if($data->reportElement->property["name"]=="writeHTML" || $data->textElement['markup']=='html')
                    $writeHTML=1;
                if(isset($data->reportElement["isPrintRepeatedValues"]))
                    $isPrintRepeatedValues=$data->reportElement["isPrintRepeatedValues"];

               
                $mydata[]=array("type"=>"MultiCell","width"=>$data->reportElement["width"]+0,"height"=>$height+0,"txt"=>$data->textFieldExpression."",
                        "border"=>$border,"align"=>$align,"fill"=>$fill,
                        "hidden_type"=>"field","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"uuid"=>$data->reportElement['uuid'],
                        "printWhenExpression"=>$data->reportElement->printWhenExpression."",
                        "link"=>$data->hyperlinkReferenceExpression."","pattern"=>$data["pattern"],"linktarget"=>$data["hyperlinkTarget"]."",
                        "writeHTML"=>$writeHTML,"isPrintRepeatedValues"=>$isPrintRepeatedValues,"rotation"=>$rotation,"valign"=>$valign,
                    "x"=>$data->reportElement["x"]+0,"y"=>$data->reportElement["y"]+0,"elementid"=>$this->elementid);
                
                
                break;
        }
        return $mydata;
        //echo  "*".$data->textFieldExpression.":".$data->reportElement['uuid']."<br/>";

    }



    public function element_Chart($data,$type='',$elementid)
    {
        $cachefolder = $this->pchartfolder.'/cache';
        if(file_exists($cachefolder))
        {
            if(!is_writable($cachefolder))
            {
                echo $this->pchartfolder.'/cache is not writable.' ;die ;
            }
        }
        else
        {
            echo $this->pchartfolder.'/cache does not exists.'  ;die;
        }

        // print_r($data->categoryDataset);
        // echo '<br/><br/>';
         $mydata=[];
         $this->elementid=$elementid;
         $height=(int)$data->chart->reportElement["height"];
         $width=(int)$data->chart->reportElement["width"];
         $x=(int)$data->chart->reportElement["x"];
         $y=(int)$data->chart->reportElement["y"];
         $charttitle=array();
         $chartsubtitle=array();
         $chartlegend=array();
         $theme=$data->chart['theme'];
         $defaultfont="times new roman";
         
         if($data->chart->chartTitle->titleExpression.""!=""){
          $charttitle['text']=$data->chart->chartTitle->titleExpression."";
          $charttitle['position']= ($data->chart->chartTitle['position']."" !="" ? $data->chart->chartTitle['position']."" :'');
          $charttitle['fontname']=($data->chart->chartTitle->font['fontName']."" ? $data->chart->chartTitle->font['fontName']."":$defaultfont);
          $charttitle['fontsize']=($data->chart->chartTitle->font['size']+0?$data->chart->chartTitle->font['size']+0:10);
          $charttitle['color']=($data->chart->chartTitle['color'].""?$data->chart->chartTitle['color']."":'000000');
          $charttitle['isBold']=($data->chart->chartTitle['isBold'].""?true:false);
          $charttitle['isUnderline']=($data->chart->chartTitle['isUnderline'].""?true:false);
          $charttitle['isItalic']=($data->chart->chartTitle['isItalic'].""?true:false);
         }
         else{
             $charttitle=null;
         }
         
          if($data->chart->chartSubtitle->subtitleExpression.""!=''){
              
             $chartsubtitle['fontname']=($data->chart->chartSubtitle->font['fontName'].""?"":$defaultfont);
             
            $chartsubtitle['text']=($data->chart->chartSubtitle->subtitleExpression.""?"":'');
             $chartsubtitle['fontsize']=($data->chart->chartSubtitle->font['size']+10?"":9);
            $chartsubtitle['color']=($data->chart->chartSubtitle['color'].""?"":'000000');
          $chartsubtitle['isBold']=($data->chart->chartSubtitle['isBold'].""?true:false);
          $chartsubtitle['isUnderline']=($data->chart->chartSubtitle['isUnderline'].""?true:false);
          $chartsubtitle['isItalic']=($data->chart->chartSubtitle['isItalic'].""?true:false);
          }
          else{
              $chartsubtitle=null;
          }
          
          if($data->chart['isShowLegend']=='true' || $data->chart['isShowLegend']==''){
          $chartlegend['position']=($data->chart->chartLegend['position'].""?"":"Right");
          $chartlegend['color']=($data->chart->chartLegend['textColor'].""?"":'#000000');
        if($data->chart->chartLegend['backgroundColor'].""=="")
            $chartlegend['backgroundColor']='#FFFFFF';
        else
            $chartlegend['backgroundColor']=$data->chart->chartLegend['backgroundColor']."";

          $chartlegend['size']=($data->chart->chartLegend['size']+0?"":9);
          $chartlegend['fontname']=($data->chart->chartLegend['fontName'].""?"":$defaultfont);
          $chartlegend['isUnderline']=($data->chart->chartLegend['isUnderline'].""?true:false);
          $chartlegend['isBold']=($data->chart->chartLegend['isBold'].""?true:false);
          $chartlegend['isItalic']=($data->chart->chartLegend['isItalic'].""?true:false);
         }else{
             
             $chartlegend=null;
             
         }
         
          /*
           * <chartLegend textColor="#666666" backgroundColor="#CCCCFF" position="Left">
                        <font fontName="Times New Roman" size="12" isUnderline="true"/>
                    </chartLegend>
           */
          $dataset=$data->categoryDataset->dataset->datasetRun['subDataset']."";


        if($type=='pieChart'){
          $dataset=$data->pieDataset->dataset->datasetRun['subDataset'];
          $seriesexp=$data->pieDataset->keyExpression;
          $valueexp=$data->pieDataset->valueExpression;
          $bb=$data->pieDataset->dataset->datasetRun['subDataset'];
          $sql=$this->arraysubdataset[$bb]['sql'];
        }
        else{
          $i=0;
           $seriesexp=array();
          $catexp=array();
          $valueexp=array();
          $labelexp=array();

          $subcatdataset=$data->categoryDataset;
          foreach($subcatdataset as $cat => $catseries){
            foreach($catseries as $a => $series){
               if("$series->categoryExpression"!=''){

                    $series->seriesExpression=  str_replace(array('"',"'"), '',$series->seriesExpression);
                    $series->categoryExpression=  str_replace(array('"',"'"), '',$series->categoryExpression);
                    $series->valueExpression=  str_replace(array('"',"'"), '',$series->valueExpression);
                    $series->labelExpression=  str_replace(array('"',"'"), '',$series->labelExpression);
       
                array_push( $seriesexp,$series->seriesExpression);
                array_push( $catexp,$series->categoryExpression);
                array_push( $valueexp,$series->valueExpression);
                array_push( $labelexp,$series->labelExpression);
                
               }

            }

          }
          $bb=$data->categoryDataset->dataset->datasetRun['subDataset'];
          $sql=$this->arraysubdataset[$bb]['sql'];

        }

          switch($type){
             case "stackedBarChart":
            case "barChart":
                $ylabel=$data->barPlot->valueAxisLabelExpression;
                $xlabel=$data->barPlot->categoryAxisLabelExpression;
                $maxy=$data->barPlot->rangeAxisMaxValueExpression;
                $miny=$data->barPlot->rangeAxisMinValueExpression;
                $valueAxisFormat['linecolor']=$data->barPlot->valueAxisFormat->axisFormat['axisLineColor']."";
                $valueAxisFormat['labelcolor']=$data->barPlot->valueAxisFormat->axisFormat['tickLabelColor']."";
                $valueAxisFormat['fontname']=$data->barPlot->valueAxisFormat->axisFormat->tickLabelFont->font['fontName']."";
                $categoryAxisFormat['linecolor']=$data->barPlot->categoryAxisFormat->axisFormat['axisLineColor']."";
                $categoryAxisFormat['labelcolor']=$data->barPlot->categoryAxisFormat->axisFormat['tickLabelColor']."";
                $categoryAxisFormat['fontname']=$data->barPlot->categoryAxisFormat->axisFormat->tickLabelFont->font['fontName']."";
                break;
            case "lineChart":
                $ylabel=$data->linePlot->valueAxisLabelExpression;
                $xlabel=$data->linePlot->categoryAxisLabelExpression;
                $maxy=$data->linePlot->rangeAxisMaxValueExpression;
                $miny=$data->linePlot->rangeAxisMinValueExpression;
                $valueAxisFormat['linecolor']=$data->linePlot->valueAxisFormat->axisFormat['axisLineColor']."";
                $valueAxisFormat['labelcolor']=$data->linePlot->valueAxisFormat->axisFormat['tickLabelColor']."";
                $valueAxisFormat['fontname']=$data->linePlot->valueAxisFormat->axisFormat->tickLabelFont->font['fontName']."";
                $categoryAxisFormat['linecolor']=$data->linePlot->categoryAxisFormat->axisFormat['axisLineColor']."";
                $categoryAxisFormat['labelcolor']=$data->linePlot->categoryAxisFormat->axisFormat['tickLabelColor']."";
                $categoryAxisFormat['fontname']=$data->linePlot->categoryAxisFormat->axisFormat->tickLabelFont->font['fontName']."";

                $showshape=$data->linePlot["isShowShapes"];
                break;
             case "stackedAreaChart":
                      $ylabel=$data->areaPlot->valueAxisLabelExpression;
                        $xlabel=$data->areaPlot->categoryAxisLabelExpression;
                        $maxy=$data->areaPlot->rangeAxisMaxValueExpression;
                        $miny=$data->areaPlot->rangeAxisMinValueExpression;
                $valueAxisFormat['linecolor']=$data->areaPlot->valueAxisFormat->axisFormat['axisLineColor']."";
                $valueAxisFormat['labelcolor']=$data->areaPlot->valueAxisFormat->axisFormat['tickLabelColor']."";
                $valueAxisFormat['fontname']=$data->areaPlot->valueAxisFormat->axisFormat->tickLabelFont->font['fontName']."";
                $valueAxisFormat['size']=$data->areaPlot->valueAxisFormat->axisFormat->tickLabelFont->font['size']+0;
                $categoryAxisFormat['linecolor']=$data->areaPlot->categoryAxisFormat->axisFormat['axisLineColor']."";
                $categoryAxisFormat['labelcolor']=$data->areaPlot->categoryAxisFormat->axisFormat['tickLabelColor']."";
                $categoryAxisFormat['fontname']=$data->areaPlot->categoryAxisFormat->axisFormat->tickLabelFont->font['fontName']."";
                $categoryAxisFormat['size']=$data->areaPlot->categoryAxisFormat->axisFormat->tickLabelFont->font['size']+0;
                 break;
          }
          


          $param=array();
          if(isset($data->categoryDataset->dataset->datasetRun->datasetParameter ))
          {
            foreach($data->categoryDataset->dataset->datasetRun->datasetParameter as $tag=>$value){
              $param[]=  array($value['name']=>$value->datasetParameterExpression);
            }  
          }
          
          if($maxy!='' && $miny!=''){
              $scalesetting=array(0=>array("Min"=>$miny,"Max"=>$maxy));
          }
          else
              $scalesetting="";

        $mydata[]=array('type'=>$type,'x'=>$x,'y'=>$y,'height'=>$height,'width'=>$width,'charttitle'=>$charttitle,
            'chartsubtitle'=> $chartsubtitle,'chartlegend'=> $chartlegend,
             'dataset'=>$dataset,'seriesexp'=>$seriesexp,
             'catexp'=>$catexp,'valueexp'=>$valueexp,'labelexp'=>$labelexp,'param'=>$param,'sql'=>$sql,
             'xlabel'=>$xlabel,'showshape'=>$showshape,  'ylabel'=>$ylabel,
             'scalesetting'=>$scalesetting,'valueAxisFormat'=>$valueAxisFormat,'categoryAxisFormat'=>$categoryAxisFormat,
             "elementid"=>$this->elementid);

        
        return $mydata;

    }

}