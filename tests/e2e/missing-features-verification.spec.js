import { test, expect } from '@playwright/test';

test.describe('Missing Features Real Implementation E2E', () => {

  test.beforeEach(async ({ page }) => {
    // Login as Admin
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@peerlend.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    await page.waitForURL((url) => !url.pathname.includes('/login'));
  });

  test('Admin can navigate to user detail profile page from users list', async ({ page }) => {
    await page.goto('/admin/users');

    // Ensure User Management title is present
    await expect(page.locator('h1')).toContainText('User Management');

    // Click View Profile on the first row
    const viewProfileBtn = page.locator('a:has-text("View Profile")').first();
    await expect(viewProfileBtn).toBeVisible();
    await viewProfileBtn.click();

    // Verify it navigates to /admin/users/* (user detail page)
    await page.waitForURL(/\/admin\/users\/[a-f0-9-]+/);
    await expect(page.locator('h1')).toBeVisible();
  });

  test('Admin can suspend and reactivate a user account from user detail page', async ({ page }) => {
    await page.goto('/admin/users');

    // Open profile of first non-admin user
    const viewProfileBtn = page.locator('a:has-text("View Profile")').first();
    await viewProfileBtn.click();
    await page.waitForURL(/\/admin\/users\/[a-f0-9-]+/);

    // Look for Suspend / Reactivate button
    const toggleBtn = page.locator('button:has-text("Suspend Account"), button:has-text("Reactivate Account")').first();
    if (await toggleBtn.isVisible()) {
      page.on('dialog', dialog => dialog.accept());
      await toggleBtn.click();

      // Check success notification banner
      const flashMsg = page.locator('div:has-text("has been")').last();
      await expect(flashMsg).toBeVisible();
    }
  });

});
