<style>
    @media print {
        @page {
            size: 70mm 22mm; /* landscape */
            /* you can also specify margins here: */
            margin: 0;
            padding: 0;
        }
    }

    .content{

    }

    .padding-left-5{
        padding-left: 5px;
    }

    .content:nth-child(2n){
        margin-left: 5%;
    }

    .content:nth-child(2n + 1){
        margin-right: 5%;
    }

    .module-float{
        display: inline-block;
        width: 100%;
        float: left;
    }
</style>

<?php
for($i = 0; $i < 2; $i++){
    ?>

    <div class="content module-float" style="width: 40%;">
        <div class="module-float" style="padding: 0 5px;">
            <div style='
                    font-family: Helvetica Neue, Helvetica, Arial, San-Serif;
                    font-weight: 500; display: inline-block; text-align: left; width: 100%; padding: 0 5px;'>
                {{$package->logistic_package_barcode}}
            </div>

            <div class="module-float" style="padding: 0 5px;">
                {!!$svg!!}
            </div>

            <div class="module-float" style="font-size: 11px; padding: 0 5px;">
                @if($package->isTransportStraight()) CT @endif
                @if($package->getOrder()) {{$package->getOrder()->destination_warehouse}} @endif
                <span class=""> {{$package->getWeightCalFee()}}kg </span>
            </div>
        </div>

    </div>

    <?php
}
?>

<script>
    window.print();
</script>