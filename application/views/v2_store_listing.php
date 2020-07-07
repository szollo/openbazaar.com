<div id="v2-storeListingContainer">
    <div id="v2-storeListingLeftSide">
        <div id="v2-listingPhotosContainer">
            <div class="v2-thumbnailColumn"></div>
            <div class="v2-gallery">
                <?php $this->load->view('listing_carousel'); ?>
            </div>
        </div>
        <div id="v2-storeListingDescription">
            <div class="v2-storeListingDescriptionTitle">Description</div>
            <div class="v2-storeListingDescriptionText">
                <?php if(isset($listing->item->description)) { ?>
                    <div><?=strip_tags($listing->item->description)?></div>
                <?php } else { ?>
                    <div class="inactive-text" style="font-size: 14px">No description entered</div>
                <?php } ?>
            </div>
        </div>
        <div id="v2-storeListingReviews">
            <div class="v2-storeListingReviewsTitle">Reviews</div>
            <?php $this->load->view('listing_reviews'); ?>
        </div>
        <div id="v2-storeListingShipping">
            <div class="v2-storeListingShippingTitle">Shipping</div>
            <?php $this->load->view('listing_shipping');?>
        </div>
        <div id="v2-storeListingReturnPolicy">
            <div class="v2-storeListingReturnPolicyTitle">Return policy</div>
            <?=(isset($listing->refundPolicy)) ? $listing->refundPolicy : ""?>
            <?php if(empty($listing->refundPolicy)) { ?>
                <div class="inactive-text" style="padding-top:10px;font-size:14px;">No return policy entered</div>
            <?php } ?>
        </div>
        <div id="v2-storeListingTOS">
            <div class="v2-storeListingTOSTitle">Terms of service</div>
            <?=(isset($listing->termsAndConditions)) ? $listing->termsAndConditions : ""?>
            <?php if(empty($listing->termsAndConditions)) { ?>
                <div class="inactive-text" style="padding-top:10px;font-size:14px;">No terms and conditions entered</div>
            <?php } ?>
        </div>
    </div>
    <div id="v2-storeListingRightSide">
        <div class="v2-storeNameLabel"><?=$profile->name?></div>
        <div class="v2-storeListingTitle"><?=$listing->item->title?></div>
        <div class="v2-storeListingMetadataContainer">
            <div class="v2-storeListingRating">⭐ <?=number_format($rating,1)?></div>
            <div class="v2-storeListingReviewCount"><?=$ratings?> reviews</div>
            <div class="v2-storeListingCondition"><?=contract_type_to_friendly($listing->metadata->contractType)?> - <?=condition_to_friendly($listing->item->condition)?></div>
        </div>
        <div class="v2-storeListingPriceContainer">
            <div class="v2-storeListingPrice">
                <?php if(isset($listing->item->bigPrice)) { ?>
                        <?=pretty_price($listing->item->bigPrice, $listing->item->priceCurrency->code)?>
                <?php } else { ?>
                        <?=pretty_price($listing->item->price, $listing->metadata->pricingCurrency)?>
                <?php } ?>
            </div>
            <?php if(!$free_shipping) { ?>
                <div class="v2-storeListingFreeShipping">
                    <div class="phraseBox">Free Shipping</div>
                </div>
            <?php } ?>
        </div>
        <div class="v2-buyNowButton">
            BUY NOW
        </div>
        <div class="v2-shareContainer">
            <div class="v2-twitterButton">
                <a href="https://twitter.com/intent/tweet?text=<?=$listing->item->title?> on @OpenBazaar http://<?=$_SERVER['HTTP_HOST']?>/<?=$listing->vendorID->peerID?>/store/<?=$listing->slug?>" target="_blank"><img src="<?=asset_url()?>img/icon-twitter.png" height=12.5 /></a>
            </div>
            <div class="v2-facebookButton">
                <a href="https://www.facebook.com/sharer/sharer.php?u=http://<?=$_SERVER['HTTP_HOST']?>/<?=$listing->vendorID->peerID?>/store/<?=$listing->slug?>" target="_blank"><img src="<?=asset_url()?>img/icon-facebook.png" height=12.5 target="_blank"/></a>
            </div>
            <div class="v2-pinterestButton">
                <a href="http://pinterest.com/pin/create/button/?url=`<?=base_url()?>`<?=$listing->vendorID->peerID?>/store/<?=$listing->slug?>&media=https://gateway.ob1.io/ob/images/<?php echo (isset($listing->item->images)) ? $listing->item->images[0]->medium . "?usecache=true": ''; ?>&description=<?=urlencode($listing->item->title)?>" target="_blank"><img src="<?=asset_url()?>img/icon-pinterest.png" height=12.5 target="_blank"/></a>
            </div>
        </div>
        <div class="v2-acceptedPaymentsContainer">
            <div class="v2-acceptedPaymentsTitle">Accepts</div>
            <?php
                $coins = array(
                    "BCH" => array("label"=>"Bitcoin Cash", "icon"=>"bchIcon128.png"),
                    "BTC" => array("label"=>"Bitcoin", "icon"=>"btcIcon128.png"),
                    "LTC" => array("label"=>"Litecoin", "icon"=>"ltcIcon128.png"),
                    "ZEC" => array("label"=>"Zcash", "icon"=>"zecIcon128.png"),
                    "ETH" => array("label"=>"Ethereum", "icon"=>"ethIcon128.png")
                );

                foreach($listing->metadata->acceptedCurrencies as $acceptedCurrency) { ?>
                <div class="v2-acceptedPaymentsRow">
                    <img class="v2-currencyIcon" src="<?=asset_url()?>img/<?=$coins[$acceptedCurrency]['icon']?>"/>
                    <?=$coins[$acceptedCurrency]['label']?>
                </div>
            <?php } ?>
        </div>
        <div class="v2-storeListingSellerInfo">
            <div class="v2-sellerLabel">Seller</div>
            <div class="v2-sellerContainer">
                <div class="v2-sellerAvatar" style="background-image: url('<?php echo (isset($profile->avatarHashes)) ? "https://gateway.ob1.io/ob/images/".$profile->avatarHashes->tiny. "?usecache=true" : asset_url().'img/defaultAvatar.png?>'; ?>');"></div>
                <div class="v2-sellerDetails">
                    <div class="v2-sellerName"><?=$profile->name?></div>
                    <div class="v2-visitStoreButton">Visit Store</div>
                </div>
            </div>
        </div>
        <div class="v2-storeListingTagsContainer">
            <div class="v2-storeListingTagsTitle">Tags</div>
            <div class="v2-storeListingTagsButtons">
                <?php foreach($listing->item->tags as $tag) { ?>
                    <a href="/discover/results?q=<?=urlencode($tag)?>" title="Search for <?=$tag?>"><div class="v2-tag"><?=$tag?></div></a>
                <?php } ?>
                <?php if(count($listing->item->tags) == 0) { ?><span class="inactive-text" style="font-size: 13px">No tags entered</span><?php } ?>

            </div>
        </div>
    </div>
</div>
<div id="v2-mayAlsoLikeContainer">
    <div class="v2-storeListingYouMayAlsoLikeTitle">You may also like</div>
    <?php $this->load->view('listing_mayalsolike');?>
</div>