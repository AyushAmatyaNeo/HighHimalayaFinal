<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Calculator</title>
    <style>
  body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.calculator {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 800px;
    width: 60px;
}

h2 {
    color: green;
    line-height: 1.5;
    margin-top: 0;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input, select {
    padding: 8px;
    margin-bottom: 16px;
    width: 100px;
    box-sizing: border-box;
    text-align: right; /* Align input text to the right */
}

button {
    padding: 8px 16px;
    background-color: #4caf50;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 16px;
}

button:hover {
    background-color: #45a049;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.result-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.result {
    padding-left: 10px;
}

.label-container, .input-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.label-container label {
    flex-basis: 40%;
}

.input-container input {
    flex-basis: 40%;
}

@media (min-width: 800px) {
    .calculator {
        width: 80%;
    }
}

.right-side-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.right-side-table caption {
    caption-side: top;
    font-weight: bold;
    margin-bottom: 10px;
}

.right-side-table th, .right-side-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.right-side-table th {
    background-color: #f0f0f0;
    color: #333;
}

.right-side-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.right-side-table tbody tr:hover {
    background-color: #f0f0f0;
}

.right-side-table .amount-cell {
    text-align: right;
}


    </style>
</head>
<body>

 
<div class="calculator">
        <h2>Salary Calculator</h2>

    <div class="label-container">
    <label for="grossSalary">Gross Salary:</label>
    <label for="basicSalaryPercentage">Basic %:</label>
    <label for="allowancePercentage">Allowance %:</label>
    <label for="pfPercentage">PF %:</label>
    <label for="gratuityPercentage">Gratuity %:</label>
    <label for="maritalStatus">Status:</label>
</div>

<div class="input-container">
    <input type="number" id="grossSalary" placeholder="Enter gross salary">
    <input type="number" id="basicSalaryPercentage" placeholder="Enter Basic %" value="54.054">
    <input type="number" id="allowancePercentage" class="percentageInput" data-formula="allowanceFormula" placeholder="Enter allowance %" value="36.036">
    <input type="number" id="pfPercentage" class="percentageInput" data-formula="pfFormula" placeholder="Enter PF %" value="5.4054">
    <input type="number" id="gratuityPercentage" class="percentageInput" data-formula="gratuityFormula" placeholder="Enter Gratuity %" value="4.5027">
    <select id="maritalStatus">
            <option value="single">Single</option>
            <option value="married">Married</option>
        </select>
</div>
 <div class="result-container">


 <h3>Total %: <span id="total">0.00</span></h3>  <div>
        <button onclick="calculateSalary()">Calculate</button>
        </div>
           
        <table class="right-side-table">
    <caption>Monthly Figures</caption>
    <thead>
        <tr>
            <th>Description</th>
            <th class="amount-cell">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Basic Salary:</td>
            <td class="amount-cell"><span id="basicSalary"></span></td>
        </tr>
        <tr>
            <td>Allowance:</td>
            <td class="amount-cell"><span id="allowance"></span></td>
        </tr>
        <tr>
            <td>PF:</td>
            <td class="amount-cell"><span id="pf"></span></td>
        </tr>
        <tr>
            <td>Gratuity:</td>
            <td class="amount-cell"><span id="gratuity"></span></td>
        </tr>
        <tr>
            <td>Total Addition:</td>
            <td class="amount-cell"><span id="addition"></span></td>
        </tr>
    </tbody>
</table>

<!-- Repeat similar structure for other tables -->


<table class="right-side-table">
    <caption>Yearly Figures</caption>
    <thead>
        <tr>
            <th>Description</th>
            <th class="amount-cell">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Basic Yearly:</td>
            <td class="amount-cell"><span id="basicyearly"></span></td>
        </tr>
        <tr>
            <td>Allowance Yearly:</td>
            <td class="amount-cell"><span id="allowanceyearly"></span></td>
        </tr>
        <tr>
            <td>PF Yearly:</td>
            <td class="amount-cell"><span id="pfyearly"></span></td>
        </tr>
        <tr>
            <td>Gratuity Yearly:</td>
            <td class="amount-cell"><span id="gratuityyearly"></span></td>
        </tr>
        <tr>
            <td>Total Yearly</td>
            <td class="amount-cell"><span id="totalyearly"></span></td>
        </tr>
    </tbody>
</table>


<table class="right-side-table">
    <caption>Deduction Part</caption>
    <thead>
        <tr>
            <th>Description</th>
            <th class="amount-cell">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>PF 20% Deduction Yearly:</td>
            <td class="amount-cell"><span id="pfded"></span></td>
        </tr>
        <tr>
            <td>Gratuity Deduction yearly:</td>
            <td class="amount-cell"><span id="gratuityded"></span></td>
        </tr>
        <tr>
            <td>Total Deduction Yearly:</td>
            <td class="amount-cell"><span id="tdyearly"></span></td>
        </tr>
    </tbody>
</table>


            <div class="result">
            <p>Total Taxable Amount: <span id="taxableamount"></span></p>
            </div>

          

            <table class="right-side-table">
            <caption>TAX Calculation</caption>
                <tr>
                    <th style="align-items: center;">Slab</th>
                    <th>Tax Slab</th>
                    <th>Base Amount</th>
                    <th>Tax On</th>
                    <th>Slab </th>
                    <th>Tax Amount</th>
                </tr>
                <tr>
                    <td>1st Slab</td>
                    <td class="amount-cell"><span id="slab1_limit"></span></td>
                    <td class="amount-cell"><span id="baseamount"></span></td>
                    <td class="amount-cell"><span id="taxon1"></span></td>
                    <td class="amount-cell">1%</td>
                    <td class="amount-cell"><span id="amount1percent"></span></td>
                </tr>
                <tr>
                    <td>2nd Slab</td>
                    <td class="amount-cell">200,000.00</td>
                    <td class="amount-cell"><span id="value1"></span></td>
                    <td class="amount-cell"><span id="taxon2"></span></td>
                    <td class="amount-cell">10%</td>
                    <td class="amount-cell"><span id="amount2percent"></span></td>
                </tr>
                <tr>
                    <td>3rd Slab</td>
                    <td class="amount-cell">300,000.00</td>
                    <td class="amount-cell"><span id="value2"></span></td>
                    <td class="amount-cell"><span id="taxon3"></span></td>
                    <td class="amount-cell">20%</td>
                    <td class="amount-cell"><span id="amount3percent"></span></td>
                </tr>
                <tr>
                    <td>4rd Slab</td>
                    <td class="amount-cell">900,000.00</td>
                    <td class="amount-cell"><span id="value3"></span></td>
                    <td class="amount-cell"><span id="taxon4"></span></td>
                    <td class="amount-cell">30%</td>
                    <td class="amount-cell"><span id="amount4percent"></span></td>
                </tr>
                <tr>
                    <td>Remaining</td>
                    <td class="amount-cell">-</td>
                    <td class="amount-cell">-</td>
                    <td class="amount-cell">-</td>
                    <td class="amount-cell">36%</td>
                  
                    <td class="amount-cell">-</td>
                </tr>
                <tr>
                    <td colspan="5" style="color: blue;" >Total TAX Per Year</td>
                    <td class="amount-cell"><span id="totaltaxperyear"></span></td>
                </tr>
                <tr>
                    <td colspan="5" style="color: blue;">Per Month Tax</td>
                    <td class="amount-cell"><span id="permonthtax"></span></td>
                </tr>

                <tr>
                    <td colspan="5" style="background-color:yellowgreen"><p style="font-weight:bold; font-size:13px;">NET salary per month</p></td>
                    <td style="background-color:yellow" class="amount-cell"><span style="font-weight:bold; font-size:13px;" id="net"></span></td>
                </tr>

            </table>
    
            </div>

           
       

      

      
    <script>
        function updateTotalPercentage() {
        var basicSalaryPercentage = parseFloat(document.getElementById("basicSalaryPercentage").value) || 0;

        var allowancePercentage = 90 - basicSalaryPercentage;

        var pfPercentage = basicSalaryPercentage / 10;

        var gratuityPercentage = 10 - pfPercentage;

        document.getElementById("allowancePercentage").value = allowancePercentage.toFixed(3);
    document.getElementById("pfPercentage").value = pfPercentage.toFixed(4);
    document.getElementById("gratuityPercentage").value = gratuityPercentage.toFixed(4);

       
        var totalPercentage = basicSalaryPercentage + allowancePercentage + pfPercentage + gratuityPercentage;

       
        document.getElementById("total").innerText = totalPercentage.toFixed(2);
    }

    var inputs = document.querySelectorAll('#basicSalaryPercentage, #allowancePercentage, #pfPercentage, #gratuityPercentage');
    inputs.forEach(function(input) {
        input.addEventListener('input', updateTotalPercentage);
    });

   
    updateTotalPercentage();

        function calculateSalary() {
            var grossSalary = parseFloat(document.getElementById("grossSalary").value);
            var basicSalaryPercentage = parseFloat(document.getElementById("basicSalaryPercentage").value);
          
            if (isNaN(grossSalary) || isNaN(basicSalaryPercentage)) {
            alert("Please enter valid numbers FOR gross and percentage value.");
            return;
        }
        var allowancePercentage = parseFloat(document.querySelector('.percentageInput[data-formula="allowanceFormula"]').value);
        var pfPercentage = parseFloat(document.querySelector('.percentageInput[data-formula="pfFormula"]').value);
        var gratuityPercentage = parseFloat(document.querySelector('.percentageInput[data-formula="gratuityFormula"]').value);

         
            var basicSalary = grossSalary * (basicSalaryPercentage / 100);
            var basicyearly = basicSalary * 12;
            var allowance = grossSalary * (allowancePercentage / 100 );
            var allowanceyearly = allowance * 12;
            var pf = grossSalary * (pfPercentage / 100 );
            var pfyearly= pf * 12;
           
            var gratuity = grossSalary * (gratuityPercentage / 100 );
            var gratuityyearly = gratuity * 12;

            var pfded = pfyearly * 2;
            var gratuityded = gratuity * 12; 

            var addition = basicSalary + allowance + pf + gratuity;
            
            var totalyearly = basicyearly + allowanceyearly + pfyearly + gratuityyearly;
            var tdyearly = pfded + gratuityded;
            var taxableamount = totalyearly - tdyearly;
            var baseamount = taxableamount;
            var maritalStatus = document.getElementById("maritalStatus").value;
            var slab1_limit = maritalStatus==='married'?600000:500000;
            
          
            var slab2_limit = 200000;
            var slab3_limit = 300000;
            var slab4_limit = 900000;
            var value1 = (taxableamount > slab1_limit) ? (taxableamount - slab1_limit) : 0;
            var value2 = (value1 > slab2_limit) ? (value1 - slab2_limit) : value1;
            var value3 = (value2 > slab3_limit) ? (value2 - slab3_limit) : value2;

            var taxon1 =  (taxableamount > slab1_limit) ? slab1_limit : taxableamount;
            var taxon2 =  (value2 > slab2_limit) ? slab2_limit : value2;
            var taxon3 = (value2  > slab3_limit) ? slab3_limit : value2;
            var taxon4 = (value3 > slab4_limit) ? (value3 - slab4_limit) : value3;

            var amount1percent = taxon1 * 0.01;
            var amount2percent = taxon2 * 0.1;
            var amount3percent = taxon3 * 0.2;
            var amount4percent = taxon4 * 0.3;
            var totaltaxperyear = amount1percent + amount2percent + amount3percent + amount4percent;
            var permonthtax =  totaltaxperyear / 12 ;
            var net = addition - ( pf * 2 ) - gratuity - permonthtax;
            var total = basicSalaryPercentage + allowancePercentage + pfPercentage + gratuityPercentage;
           
           
         if (total > 100) {
        alert("Total percentage cannot exceed 100%. Please adjust your input values.");
        return;
              }

            document.getElementById("basicSalary").innerText = basicSalary.toFixed(2);
            document.getElementById("basicyearly").innerText = basicyearly.toFixed(2);
            document.getElementById("allowance").innerText = allowance.toFixed(2);
            document.getElementById("allowanceyearly").innerText = allowanceyearly.toFixed(2);
            document.getElementById("pf").innerText = pf.toFixed(2);
            document.getElementById("pfyearly").innerText = pfyearly.toFixed(2);
            document.getElementById("gratuity").innerText = gratuity.toFixed(2);
            document.getElementById("gratuityyearly").innerText = gratuityyearly.toFixed(2);
            document.getElementById("addition").innerText = addition.toFixed(2);
            document.getElementById("pfded").innerText = pfded.toFixed(2);
            document.getElementById("gratuityded").innerText = gratuityded.toFixed(2);
            document.getElementById("totalyearly").innerText = totalyearly.toFixed(2);
            document.getElementById("tdyearly").innerText = tdyearly.toFixed(2);
            document.getElementById("taxableamount").innerText = taxableamount.toFixed(2);
            document.getElementById("baseamount").innerText = baseamount.toFixed(2);
            document.getElementById("slab1_limit").innerText = slab1_limit.toFixed(2);
            document.getElementById("value1").innerText = value1.toFixed(2);
            document.getElementById("value2").innerText = value2.toFixed(2);
            document.getElementById("value3").innerText = value3.toFixed(2);
            document.getElementById("taxon1").innerText = taxon1.toFixed(2);
            document.getElementById("taxon2").innerText = taxon2.toFixed(2);
            document.getElementById("taxon3").innerText = taxon3.toFixed(2);
            document.getElementById("taxon4").innerText = taxon4.toFixed(2);
            document.getElementById("amount1percent").innerText = amount1percent.toFixed(2);
            document.getElementById("amount2percent").innerText = amount2percent.toFixed(2);
            document.getElementById("amount3percent").innerText = amount3percent.toFixed(2);
            document.getElementById("amount4percent").innerText = amount4percent.toFixed(2);
            document.getElementById("totaltaxperyear").innerText = totaltaxperyear.toFixed(2);
            document.getElementById("permonthtax").innerText = permonthtax.toFixed(2);
            document.getElementById("net").innerText = net.toFixed(2);
            document.getElementById("total").innerText = total.toFixed(2);
        }
    </script>
</body>
</html>
