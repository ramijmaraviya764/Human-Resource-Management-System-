<!-- Process Payroll Modal -->
<div class="modal fade" id="processPayrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calculator"></i> Process Salary Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Enter Employee ID</label>
                    <input type="text" class="form-control" id="empIdInput" placeholder="e.g., EMP-2024-001">
                    <button class="btn btn-primary mt-2" id="fetchEmployeeBtn">
                        <i class="fas fa-search"></i> Fetch Employee Data
                    </button>
                </div>

                <div id="employeeDataSection" style="display: none;">
                    <div class="attendance-warning" id="attendanceWarning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Attendance is below 85%. Salary will be deducted proportionally.
                    </div>

                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="fas fa-user"></i> Employee Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> <span id="empName"></span><br>
                                <strong>Department:</strong> <span id="empDept"></span><br>
                                <strong>Email:</strong> <span id="empEmail"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Total Working Days:</strong> <span id="empWorkingDays"></span><br>
                                <strong>Days Present:</strong> <span id="empPresent"></span><br>
                                <strong>Days Absent:</strong> <span id="empAbsent"></span><br>
                                <strong>Attendance %:</strong> <span id="empAttendance"></span>
                            </div>
                        </div>
                    </div>

                    <div class="calculation-summary">
                        <h6 style="color: #2d3748; margin-bottom: 15px;">
                            <i class="fas fa-calculator"></i> Salary Calculation
                        </h6>
                        <div class="summary-row">
                            <span>Basic Salary</span>
                            <span id="calcBasic">₹0</span>
                        </div>
                        <div class="summary-row">
                            <span>Allowances (HRA + Transport + Special)</span>
                            <span id="calcAllowances">₹0</span>
                        </div>
                        <div class="summary-row">
                            <span>Gross Salary</span>
                            <span id="calcGross">₹0</span>
                        </div>
                        <div class="summary-row" id="attendanceDeductionRow" style="display: none; color: #f56565;">
                            <span>Attendance Deduction</span>
                            <span id="calcAttendanceDeduction">₹0</span>
                        </div>
                        <div class="summary-row" style="color: #f56565;">
                            <span>Standard Deductions (PF + Tax + TDS)</span>
                            <span id="calcDeductions">₹0</span>
                        </div>
                        <div class="summary-row">
                            <span>Net Payable Salary</span>
                            <span id="calcNet">₹0</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="">Select Payment Method</option>
                            <option value="bank">Bank Transfer (NEFT/RTGS/IMPS)</option>
                            <option value="upi">UPI Payment</option>
                            <option value="cash">Cash Payment</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div id="bankTransferDetails" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-building"></i> <strong>Bank Transfer Details</strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" class="form-control" id="bankAccountName" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Account Number</label>
                                <input type="text" class="form-control" id="bankAccountNumber" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" class="form-control" id="bankIFSC" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bankName" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transfer Type</label>
                            <select class="form-select" id="transferType">
                                <option value="NEFT">NEFT (National Electronic Funds Transfer)</option>
                                <option value="RTGS">RTGS (Real Time Gross Settlement)</option>
                                <option value="IMPS">IMPS (Immediate Payment Service)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transaction Reference Number</label>
                            <input type="text" class="form-control" id="bankTransactionRef" placeholder="Enter UTR/Transaction ID">
                        </div>
                    </div>

                    <!-- UPI Details -->
                    <div id="upiDetails" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fab fa-google-pay"></i> <strong>UPI Payment Details</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company UPI ID (Paying From)</label>
                            <input type="text" class="form-control" id="companyUpiId" value="dayflow.hrms@okicici" readonly style="background-color: #f0f8ff; font-weight: 600;">
                            <small class="text-muted">This is your company's UPI ID for making payments</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Employee UPI ID (Paying To)</label>
                            <input type="text" class="form-control" id="upiId" readonly style="background-color: #f7fafc;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">UPI App Used</label>
                            <select class="form-select" id="upiApp">
                                <option value="GooglePay">Google Pay</option>
                                <option value="PhonePe">PhonePe</option>
                                <option value="Paytm">Paytm</option>
                                <option value="BHIM">BHIM UPI</option>
                                <option value="Other">Other UPI App</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cash Details -->
                    <div id="cashDetails" class="mt-3" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-money-bill-alt"></i> <strong>Cash Payment Details</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Received By (Employee Signature)</label>
                            <input type="text" class="form-control" id="cashReceivedBy" placeholder="Enter employee name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Paid By (Cashier/HR Name)</label>
                            <input type="text" class="form-control" id="cashPaidBy" placeholder="Enter your name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Receipt Number</label>
                            <input type="text" class="form-control" id="cashReceiptNumber" placeholder="Enter receipt number">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cashConfirmation">
                            <label class="form-check-label" for="cashConfirmation">
                                I confirm that cash has been handed over to the employee
                            </label>
                        </div>
                    </div>

                    <!-- Cheque Details -->
                    <div id="chequeDetails" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-money-check"></i> <strong>Cheque Payment Details</strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Number</label>
                                <input type="text" class="form-control" id="chequeNumber" placeholder="Enter cheque number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Date</label>
                                <input type="date" class="form-control" id="chequeDate">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="chequeBank" placeholder="Enter issuing bank name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch</label>
                                <input type="text" class="form-control" id="chequeBranch" placeholder="Enter branch name">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payee Name</label>
                            <input type="text" class="form-control" id="chequePayee" readonly>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="paymentDate" required>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Additional Remarks (Optional)</label>
                        <textarea class="form-control" id="paymentRemarks" rows="2" placeholder="Enter any additional notes about this payment"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="saveForLaterBtn" style="display: none;">
                    <i class="fas fa-save"></i> Save for Later
                </button>
                <button type="button" class="btn btn-success" id="processSalaryBtn" style="display: none;">
                    <i class="fas fa-check-circle"></i> Process Payment & Send Payslip
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payslip Preview Modal -->
<div class="modal fade" id="payslipModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice"></i> Payslip Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="payslipPreview">
                    <div class="payslip-header-section">
                        <div class="company-info">
                            <h2>DAYFLOW HRMS</h2>
                            <p>123 Business Park, Tech City, IN 560001</p>
                            <p>Email: hr@dayflow.com | Phone: +91-9876543210</p>
                            <div class="payslip-title">
                                SALARY SLIP - <span id="payslipMonth">JANUARY 2026</span>
                            </div>
                        </div>
                    </div>

                    <div class="employee-info-grid">
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Employee Name</span>
                            <span class="info-value-payslip" id="payslipEmpName">-</span>
                        </div>
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Employee ID</span>
                            <span class="info-value-payslip" id="payslipEmpId">-</span>
                        </div>
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Department</span>
                            <span class="info-value-payslip" id="payslipDept">-</span>
                        </div>
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Designation</span>
                            <span class="info-value-payslip" id="payslipDesignation">-</span>
                        </div>
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Pay Period</span>
                            <span class="info-value-payslip" id="payslipPeriod">-</span>
                        </div>
                        <div class="info-item-payslip">
                            <span class="info-label-payslip">Payment Date</span>
                            <span class="info-value-payslip" id="payslipPayDate">-</span>
                        </div>
                    </div>

                    <h6 style="color: #2d3748; margin: 20px 0 10px; font-weight: 600;">
                        <i class="fas fa-calendar-check"></i> Attendance Details
                    </h6>
                    <table class="salary-table">
                        <tr>
                            <td><strong>Total Working Days:</strong></td>
                            <td id="payslipWorkDays">-</td>
                            <td><strong>Days Present:</strong></td>
                            <td id="payslipPresent">-</td>
                        </tr>
                        <tr>
                            <td><strong>Days Absent:</strong></td>
                            <td id="payslipAbsent">-</td>
                            <td><strong>Attendance Percentage:</strong></td>
                            <td id="payslipAttendancePer">-</td>
                        </tr>
                        <tr>
                            <td><strong>Paid Leave Taken:</strong></td>
                            <td id="payslipLeave">-</td>
                            <td><strong>Unpaid Leave:</strong></td>
                            <td id="payslipUnpaidLeave">-</td>
                        </tr>
                    </table>

                    <h6 style="color: #48bb78; margin: 20px 0 10px; font-weight: 600;">
                        <i class="fas fa-plus-circle"></i> Earnings
                    </h6>
                    <table class="salary-table">
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th style="text-align: right;">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td style="text-align: right;" id="payslipBasic">-</td>
                            </tr>
                            <tr>
                                <td>House Rent Allowance (HRA)</td>
                                <td style="text-align: right;" id="payslipHRA">-</td>
                            </tr>
                            <tr>
                                <td>Transport Allowance</td>
                                <td style="text-align: right;" id="payslipTransport">-</td>
                            </tr>
                            <tr>
                                <td>Special Allowance</td>
                                <td style="text-align: right;" id="payslipSpecial">-</td>
                            </tr>
                            <tr style="background: #f7fafc; font-weight: bold;">
                                <td>Gross Earnings</td>
                                <td style="text-align: right;" id="payslipGrossEarn">-</td>
                            </tr>
                        </tbody>
                    </table>

                    <h6 style="color: #f56565; margin: 20px 0 10px; font-weight: 600;">
                        <i class="fas fa-minus-circle"></i> Deductions
                    </h6>
                    <table class="salary-table">
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th style="text-align: right;">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="attendanceDeductionRowPayslip" style="display: none;">
                                <td>Attendance Deduction (Below 85%)</td>
                                <td style="text-align: right; color: #f56565; font-weight: bold;" id="payslipAttDeduct">-</td>
                            </tr>
                            <tr>
                                <td>Provident Fund (PF)</td>
                                <td style="text-align: right;" id="payslipPF">-</td>
                            </tr>
                            <tr>
                                <td>Professional Tax</td>
                                <td style="text-align: right;" id="payslipTax">-</td>
                            </tr>
                            <tr>
                                <td>Income Tax (TDS)</td>
                                <td style="text-align: right;" id="payslipTDS">-</td>
                            </tr>
                            <tr style="background: #f7fafc; font-weight: bold;">
                                <td>Total Deductions</td>
                                <td style="text-align: right;" id="payslipTotalDeduct">-</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="salary-table" style="margin-top: 20px;">
                        <tr class="total-row-payslip">
                            <td style="padding: 15px;">NET PAYABLE SALARY</td>
                            <td style="text-align: right; padding: 15px; font-size: 20px;" id="payslipNetPay">-</td>
                        </tr>
                    </table>

                    <div class="payslip-footer">
                        <p><strong>Payment Method:</strong> <span id="payslipPayMethod">-</span></p>
                        <p style="margin-top: 20px;">This is a computer-generated payslip and does not require a signature.</p>
                        <p>For any queries, please contact HR Department at hr@dayflow.com</p>
                        <p style="margin-top: 15px; font-size: 10px;">Generated on: <span id="payslipGenDate">-</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadPDFBtn">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button type="button" class="btn btn-success" id="sendEmailBtn">
                    <i class="fas fa-envelope"></i> Send via Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Payment Modal -->
<div class="modal fade" id="bulkPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users"></i> Bulk Salary Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You have selected <strong id="bulkSelectedCount">0</strong> employees for salary payment.
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method for All Selected</label>
                    <select class="form-select" id="bulkPaymentMethod" required>
                        <option value="">Select Payment Method</option>
                        <option value="bank">Bank Transfer (NEFT/RTGS/IMPS)</option>
                        <option value="upi">UPI Payment</option>
                        <option value="cash">Cash Payment</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <!-- Bulk Bank Transfer -->
                <div id="bulkBankDetails" style="display: none;">
                    <div class="alert alert-success">
                        <i class="fas fa-building"></i> <strong>Bank Transfer Mode</strong> - All employees will receive payment via their registered bank accounts.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transfer Type</label>
                        <select class="form-select" id="bulkTransferType">
                            <option value="NEFT">NEFT (National Electronic Funds Transfer)</option>
                            <option value="RTGS">RTGS (Real Time Gross Settlement)</option>
                            <option value="IMPS">IMPS (Immediate Payment Service)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Reference Number</label>
                        <input type="text" class="form-control" id="bulkBatchReference" placeholder="Enter batch transaction reference">
                    </div>
                </div>

                <!-- Bulk UPI -->
                <div id="bulkUpiDetails" style="display: none;">
                    <div class="alert alert-success">
                        <i class="fab fa-google-pay"></i> <strong>UPI Payment Mode</strong> - All employees will receive payment via their registered UPI IDs.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company UPI ID (Paying From)</label>
                        <input type="text" class="form-control" value="dayflow.hrms@okicici" readonly style="background-color: #f0f8ff; font-weight: 600;">
                        <small class="text-muted">Company's UPI ID for bulk salary payments</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UPI App Used</label>
                        <select class="form-select" id="bulkUpiApp">
                            <option value="GooglePay">Google Pay</option>
                            <option value="PhonePe">PhonePe</option>
                            <option value="Paytm">Paytm</option>
                            <option value="BHIM">BHIM UPI</option>
                            <option value="Other">Other UPI App</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Cash Warning -->
                <div id="bulkCashDetails" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Cash payment for multiple employees requires physical handover and individual receipts.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid By (Cashier/HR Name)</label>
                        <input type="text" class="form-control" id="bulkCashPaidBy" placeholder="Enter your name">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="bulkCashConfirmation">
                        <label class="form-check-label" for="bulkCashConfirmation">
                            I confirm that cash will be handed over to all selected employees individually
                        </label>
                    </div>
                </div>

                <!-- Bulk Cheque -->
                <div id="bulkChequeDetails" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-money-check"></i> <strong>Cheque Payment Mode</strong> - Individual cheques will be issued to all selected employees.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cheque Series Start</label>
                            <input type="text" class="form-control" id="bulkChequeStart" placeholder="Starting cheque number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cheque Date</label>
                            <input type="date" class="form-control" id="bulkChequeDate">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issuing Bank</label>
                        <input type="text" class="form-control" id="bulkChequeBank" placeholder="Enter bank name">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="bulkPaymentDate" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks (Optional)</label>
                    <textarea class="form-control" id="bulkRemarks" rows="2" placeholder="Enter any notes for this bulk payment"></textarea>
                </div>

                <div class="calculation-summary">
                    <h6 style="color: #2d3748; margin-bottom: 15px;">
                        <i class="fas fa-calculator"></i> Bulk Payment Summary
                    </h6>
                    <div class="summary-row">
                        <span>Total Employees Selected</span>
                        <span id="bulkSummaryCount">0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Gross Amount</span>
                        <span id="bulkSummaryGross">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Deductions</span>
                        <span id="bulkSummaryDeductions">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Net Payable</span>
                        <span id="bulkSummaryNet">₹0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="processBulkPaymentBtn">
                    <i class="fas fa-check-double"></i> Process All Payments & Send Payslips
                </button>
            </div>
        </div>
    </div>
</div>