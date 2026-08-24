import { test, expect } from '@playwright/test';

test.describe('Customer Service (CS) and Credit Risk / Collection (CR) Roles E2E Verification', () => {

  test('CS role can access KYC review and User Management, but is denied Financial Config', async ({ page }) => {
    await page.context().clearCookies();

    // Login as CS User
    await page.goto('http://localhost:9090/login');
    await page.fill('input[name="email"]', 'cs1@lendflow.com');
    await page.fill('input[name="password"]', 'password123');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }),
      page.click('button[type="submit"]')
    ]);

    // 1. Verify CS can access Review KYC page
    await page.goto('http://localhost:9090/admin/kyc');
    await expect(page.locator('h1')).toContainText(/KYC Review Queue|Antrean Peninjauan KYC/i);

    // 2. Verify CS can access User Directory Management page
    await page.goto('http://localhost:9090/admin/users');
    await expect(page.locator('h1')).toContainText(/User Management|Manajemen Pengguna/i);

    // 3. Verify CS cannot access Admin Financial Config (returns 403 Forbidden)
    const response = await page.goto('http://localhost:9090/admin/financials');
    expect(response?.status()).toBe(403);
  });

  test('CR / Collection Officer role can access Transactions Audit, but is denied KYC review', async ({ page }) => {
    await page.context().clearCookies();

    // Login as Collection Officer (CR) User
    await page.goto('http://localhost:9090/login');
    await page.fill('input[name="email"]', 'collector1@lendflow.com');
    await page.fill('input[name="password"]', 'password123');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }),
      page.click('button[type="submit"]')
    ]);

    // 1. Verify CR can access Transactions Audit page
    await page.goto('http://localhost:9090/admin/transactions');
    await expect(page.locator('h1')).toContainText(/Transaction Monitoring|Pemantauan Transaksi|Transaction Audit Monitoring/i);

    // 2. Verify CR cannot access KYC Verification page (returns 403 Forbidden)
    const response = await page.goto('http://localhost:9090/admin/kyc');
    expect(response?.status()).toBe(403);
  });

});
