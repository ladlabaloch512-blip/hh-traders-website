# H.H Traders - Production Website (PHP + Admin Panel)

Fire safety & safety equipment store for H.H Traders, Saddar, Rawalpindi.
Built to the client's 12-point brief (14 Nov 2026): mobile design matched to the
reference screenshot, admin-managed sliders, optional prices with
"Request for Quotation", Shop Now / WhatsApp button modes, checkout-to-WhatsApp,
and Rawalpindi-focused SEO.

## Requirements
- PHP 8+ web hosting (Apache with .htaccess, e.g. Hostinger). No database needed:
  all content lives in `data.json` (managed by the admin panel).
- The `uploads/` folder and `data.json` must be writable by PHP.

## Install
1. Upload ALL files and folders to the domain's public folder (public_html).
2. Open https://YOUR-DOMAIN/ - the shop is live.
3. Admin panel: https://YOUR-DOMAIN/admin/
   - Username: admin
   - Password: HHTraders@2026  (change it: see admin/credentials.php)

## Admin panel
- Products: add/edit/delete products and photos. Price is OPTIONAL - leave it
  empty (or tick "Request for Quotation") and the product shows
  "Request for Quotation" instead of a price, exactly like the reference design.
  Tick "New" for the NEW badge, "Feature" shows the product on the home page.
- Sliders: replace home slider images and text (first slider's top heading is
  "General Order Supplier of").
- Settings: choose the product button style for the whole site -
  Shop Now (cart + checkout), WhatsApp (order on WhatsApp), or
  Request for Quotation. Also WhatsApp number, email, phones, address.
- Orders: every checkout order is saved here AND sent to the shop WhatsApp
  number (+92 312 5488700) with the full product list.

## SEO (Rawalpindi)
Page titles/descriptions, LocalBusiness schema, geo tags, sitemap.xml and
robots.txt are included. After the domain is final, update "Website address"
in Admin > Settings and submit sitemap.xml in Google Search Console.
