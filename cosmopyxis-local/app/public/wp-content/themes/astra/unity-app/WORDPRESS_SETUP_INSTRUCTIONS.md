# WordPress Setup Instructions for Cosmopyxis Unity App

## One-Time WordPress Admin Setup Required

To complete the integration, you need to create a WordPress page in the admin:

### Steps:
1. **Log into WordPress Admin**: Go to your site's `/wp-admin/`
2. **Create New Page**: 
   - Go to Pages → Add New
   - Title: "Cosmopyxis App" (or whatever you prefer)
   - Content: You can add any intro text you want above the Unity app
3. **Set Page Template**:
   - In the Page Attributes box (right sidebar)
   - Template: Select "Cosmopyxis Unity App"
4. **Publish the Page**
5. **Note the URL**: The page will be available at `/cosmopyxis-app/` (or whatever slug you choose)

### Technical Details:
- Unity files are automatically deployed to: `/wp-content/themes/astra/unity-app/`
- Page template: `page-unity-app.php` (already created)
- Each new build updates the Unity files automatically
- The WordPress page stays the same - no need to recreate it

### Styling:
The page includes cosmic-themed styling that matches the Cosmopyxis brand with:
- Deep space gradient background
- Responsive Unity container
- Loading progress bar
- Mobile device detection and warnings

### Build Information:
- Product: Cosmopyxis
- Scenes: Firebase Login → Onboarding → AI Testing → Natal Chart
- Build Date: $(date)
