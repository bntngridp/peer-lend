import { test, expect } from '@playwright/test';

test.describe('Borrower Application & Collateral E2E', () => {
  test.beforeEach(async ({ page }) => {
    // Login as Borrower (borrower1@lendflow.com)
    await page.goto('http://localhost:9090/login');
    await page.fill('input[name="email"]', 'borrower1@lendflow.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Wait for Dashboard
    await page.waitForURL('http://localhost:9090/dashboard');
  });

  test('Borrower can view My Loans page and navigate to Apply Loan form', async ({ page }) => {
    // Navigate to My Loans
    await page.goto('http://localhost:9090/loans');
    await expect(page.locator('h1')).toContainText('My Loans');

    // Click Apply New Loan button
    const applyBtn = page.locator('a:has-text("Apply for New Loan")').first();
    await applyBtn.click();

    // Verify Apply Loan page
    await page.waitForURL('http://localhost:9090/loans/create');
    await expect(page.locator('h1')).toContainText(/Calculate & Apply|Hitung & Ajukan Pinjaman/i);
  });

  test('Borrower can click View Schedule / Lihat Jadwal and navigate to installments schedule view', async ({ page }) => {
    // Navigate to My Loans
    await page.goto('http://localhost:9090/loans');
    await expect(page.locator('h1')).toContainText(/My Loans|Pinjaman Saya/i);

    // Click View Schedule button
    const viewScheduleBtn = page.locator('a:has-text("View Schedule"), a:has-text("Lihat Jadwal"), a:has-text("View Details"), a:has-text("Lihat Detail")').first();
    await expect(viewScheduleBtn).toBeVisible();
    await viewScheduleBtn.click();

    // Verify installments schedule page is loaded
    await expect(page.locator('h1')).toContainText(/Repayment Schedule|Jadwal Pembayaran/i);
    await expect(page.locator('body')).toContainText(/Installments Schedule|Jadwal Angsuran/i);
  });
});
