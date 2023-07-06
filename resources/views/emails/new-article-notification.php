<div>
    <h1 class="fa-font" style="font-size: large; font-weight:700; word-break: break-all;"><?php echo $title; ?></h1>
    <div id="img">
        <img src="<?php echo $imgURL?>" style="width: 100%; height: 100%" alt="">
    </div>
    <p style="font-size: 16px;" class="fa-cont"><?php echo $bodyText; ?></p>


</div>

<style>
    
    @media (max-width : 380px) {
        #img {
            width: 240px;
            height: 181px;
        }

    }

    #img {
        width: 481px;
        height: 302px;
    }

    @font-face {
        font-family: fa;
        /* src: url(../assets/font/MyanmarSansPro-Regular.ttf); */
        src: url('{{asset("assets/Mm3Web_V3.000-7209c674.ttf")}}');
        }

    .fa-font {
    font-family: fa;
    word-break: break-all;
  }
</style>