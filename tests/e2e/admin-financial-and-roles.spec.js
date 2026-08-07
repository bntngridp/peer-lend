import { test, expect } from '@playwright/test';

test.describe('Admin Financial Configuration & Role Management E2E', () => {

  test.beforeEach(async ({ page }) => {
    // Login as Admin
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@peerlend.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Verify logged in
    await page.waitForURL((url) => !url.pathname.includes('/login'));
  });

  test('Admin can view, edit, and save Financial Configuration', async ({ page }) => {
    await page.goto('/admin/financials');

    // Check page header
    await expect(page.locator('h1')).toContainText('Financial Configuration');

    // 1. Update Interest Rates
    await page.fill('input[name="grade_a_min"]', '4.50');
    await page.fill('input[name="grade_a_max"]', '7.50');
    await page.click('button:has-text("Update Rates")');

    // Verify success alert or flash message
    await expect(page.locator('body')).toContainText('Interest rate brackets updated successfully.');

    // Verify values persisted on reload
    await page.reload();
    await expect(page.locator('input[name="grade_a_min"]')).toHaveValue('4.50');
    await expect(page.locator('input[name="grade_a_max"]')).toHaveValue('7.50');

    // 2. Update Fee Schedule
    await page.fill('input[name="origination_fee"]', '2.25');
    await page.fill('input[name="service_fee"]', '7500');
    await page.fill('input[name="penalty_rate"]', '0.20');
    await page.click('button:has-text("Save Fee Schedule")');

    // Verify success message
    await expect(page.locator('body')).toContainText('Fee schedule saved successfully.');

    // Verify values persisted on reload
    await page.reload();
    await expect(page.locator('input[name="origination_fee"]')).toHaveValue('2.25');
    await expect(page.locator('input[name="service_fee"]')).toHaveValue('7500');

    // 3. Update Currency Settings
    await page.click('button:has-text("Save Currencies")');
    await expect(page.locator('body')).toContainText('Currency settings updated successfully.');
  });

  test('Admin can view, create role, and update permissions in Role Management', async ({ page }) => {
    await page.goto('/admin/roles');

    // Check page header
    await expect(page.locator('h1')).toContainText('Role Management');

    // Create a new custom role
    await page.click('button:has-text("+ New Role")');

    const timestamp = Date.now();
    const roleKey = `test_role_${timestamp}`;
    const roleDesc = `E2E Test Role ${timestamp}`;

    await page.fill('input[name="name"]', roleKey);
    await page.fill('input[name="description"]', roleDesc);
    await page.click('button:has-text("Confirm & Create Role")');

    // Verify created message
    await expect(page.locator('body')).toContainText(`created successfully.`);

    // Verify custom role is displayed on page
    await expect(page.locator('body')).toContainText(roleKey);

    // Save permissions for a role
    const saveButtons = page.locator('button:has-text("Save Permissions")');
    if (await saveButtons.count() > 0) {
      await saveButtons.first().click();
      await expect(page.locator('body')).toContainText('updated successfully.');
    }
  });

});
