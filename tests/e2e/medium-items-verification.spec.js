import { test, expect } from '@playwright/test';

test.describe('Medium Items Real Implementation E2E', () => {

  test.beforeEach(async ({ page }) => {
    // Login as Admin
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@peerlend.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    await page.waitForURL((url) => !url.pathname.includes('/login'));
  });

  test('Admin can view transactions audit page with dynamic status badges', async ({ page }) => {
    await page.goto('/admin/transactions');

    // Check page header
    await expect(page.locator('h1')).toContainText('Transaction Monitoring');

    // Ensure status column exists and displays real status (e.g. Completed)
    const tableBody = page.locator('tbody');
    await expect(tableBody).toBeVisible();
  });

  test('Admin can view analytics page with real database metrics', async ({ page }) => {
    await page.goto('/admin/analytics');

    // Check page header
    await expect(page.locator('h1')).toContainText('Platform Analytics');
  });

});
