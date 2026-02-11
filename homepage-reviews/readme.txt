=== Homepage Reviews ===
Contributors: vikas
Tags: reviews, testimonials, slider, homepage, reviews slider
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display beautiful, customizable review cards with a gallery slider on your homepage.

== Description ==

Homepage Reviews lets you showcase customer testimonials in a sleek, responsive slider. Each review card supports:

* Star rating (1–5, half-star increments)
* Reviewer name, position/title, and verified badge
* Up to 4 gallery images per review with adaptive grid layouts
* Customizable colors, font size, and border radius
* Autoplay with configurable speed and pause-on-hover
* Drag-and-drop reordering in the admin

**Usage:** Add the shortcode `[homepage_reviews]` to any page or post.

You can also limit the number of reviews shown: `[homepage_reviews limit="5"]`

== Installation ==

1. Upload the `homepage-reviews` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Homepage Reviews → Add New** to create your first review.
4. Customize colors and slider settings under **Homepage Reviews → Settings**.
5. Add the shortcode `[homepage_reviews]` to any page or post.

== Frequently Asked Questions ==

= How do I display reviews? =

Use the shortcode `[homepage_reviews]` on any page, post, or widget that supports shortcodes.

= Can I limit the number of reviews shown? =

Yes. Use `[homepage_reviews limit="3"]` to show only 3 reviews.

= How do I reorder reviews? =

Go to **Homepage Reviews → All Reviews**. Drag the handle (☰) on the left of each row to reorder.

= Does the slider autoplay? =

Yes. Go to **Homepage Reviews → Settings → Slider Settings** and enable Autoplay. You can also set the speed in milliseconds.

== Screenshots ==

1. Review slider on the frontend.
2. Admin settings page with style and slider options.
3. Review editor with gallery upload and meta fields.

== Changelog ==

= 1.0.1 =
* Security improvements: Output escaping and input sanitization.
* Compliance fixes for WordPress.org submission.

= 1.0.0 =
* Initial release.
* Custom post type for reviews with star rating, position, tag, and verified badge.
* Gallery grid with adaptive layouts (1–4 images).
* Frontend slider with prev/next navigation, dots, and autoplay.
* Admin settings for colors, font size, border radius, and autoplay.
* Drag-and-drop reordering.
* Demo data import.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
