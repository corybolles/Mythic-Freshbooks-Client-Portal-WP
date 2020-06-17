## Watch Tutorial Video
The video version of the instructions below can be found here: https://www.youtube.com/watch?v=m_bflx_1AQs

## STEP 1) WordPress Installation & Activation
- Log in to your WordPress admin panel
- Go to Plugins > Add New
- Click "Upload" and click "Choose File"
- Choose the installable Zip file and click "Upload"
- Once the upload finishes, click "Activate"
- Once the activation finished, go to Settings > Freshbooks Client Portal

## STEP 2) Connecting With Freshbooks
- Log in to Freshbooks
- Visit https://my.freshbooks.com/#/developer and click "Create an App"
- Fill out the application form with the following settings*:
 - **Application Name:** WordPress Client Portal
 - **Description:** Provides client portal functionality on my WordPress website.
 - **Website URL:** `[YOUR WEBSITE URL]`
 - **Application Settings URL:**  `[YOUR WEBSITE URL] /wp-admin/options-general.php?page=mythic-fcp`
 - **Redirect URLs:** `[YOUR WEBSITE URL] /wp-admin/options-general.php`

  *Make sure to include the protocol (http:// or https:// in all URLs and URIs)

- Click Save
- Click the arrow next to the application to expand the info
- Copy the "Client ID" and "Client Secret" from this page into the settings page we have open in WordPress
- Click "Save" under the fields
- Click "Connect with Freshbooks" - It will say Connected and show a checkmark when it's connected successfully
- Choose your Account/Business (Both Should Be The Same) and click Save.


## STEP 3) Adding the Shortcode
- Create a page in WordPress for the Client Portal
- Add the shortode `[fcp_client]` to the page.
- The page will now show client invoices when a client logs in*

  *Freshbooks pulls data based on the email address associated with the WordPress account. For example, if a client's email address in Freshbooks is test@example.com, they will have to have an account on your WordPress website with the same email address in order to see their data.