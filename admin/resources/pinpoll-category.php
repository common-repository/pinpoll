<?php
/**
 * Resource "Categories" from Pinpoll
 *
 * Description: All categories from Pinpoll, which are used in the
 *              in table of page "Polls" in admin menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/resources
 *
 */

/**
 * Categories
 * Description: Return all categories which exist.
 *
 * @return array categories
 */
function pinpoll_get_category_labels()
{
  return [
    'arts_ent' => __( 'Arts & Entertainment', 'pinpoll' ),
    'automotive' => __( 'Automotive', 'pinpoll' ),
    'business' => __( 'Business', 'pinpoll' ),
    'careers' => __( 'Careers', 'pinpoll' ),
    'education' => __( 'Education', 'pinpoll' ),
    'family' => __( 'Family & Parenting', 'pinpoll' ),
    'health' => __( 'Health & Fitness', 'pinpoll' ),
    'food_drink' => __( 'Food & Drink', 'pinpoll' ),
    'hobbies' => __( 'Hobbies & Interest', 'pinpoll' ),
    'home' => __( 'Home & Garden', 'pinpoll' ),
    'politics' => __( 'Law, Government & Politics', 'pinpoll' ),
    'news' => __( 'News', 'pinpoll' ),
    'finance' => __( 'Personal Finance', 'pinpoll' ),
    'society' => __( 'Society', 'pinpoll' ),
    'science' => __( 'Science', 'pinpoll' ),
    'pets' => __( 'Pets', 'pinpoll' ),
    'sports' => __( 'Sports', 'pinpoll' ),
    'fashion' => __( 'Style & Fashion', 'pinpoll' ),
    'technology' => __( 'Technology & Computing', 'pinpoll' ),
    'travel' => __( 'Travel', 'pinpoll' ),
    'real_estate' => __( 'Real Estate', 'pinpoll' ),
    'shopping' => __( 'Shopping', 'pinpoll' ),
    'religion' => __( 'Religion & Spirituality', 'pinpoll' ),
    'other' => __( 'Other', 'pinpoll' )
    ];
}
?>
