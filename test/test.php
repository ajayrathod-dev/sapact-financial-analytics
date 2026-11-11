<!DOCTYPE html>
<html>
<head>
  <title>Liability → Category → Sub-category</title>
</head>
<body>

<h3>Select Liability → Category → Sub-category</h3>

<form>
  <label>Liability:</label>
  <select id="liability" onchange="loadCategories()">
    <option value="">--Select--</option>
    <option value="Liabilities">Liabilities</option>
    <option value="Assets">Assets</option>
  </select>

  <br><br>

  <label>Category:</label>
  <select id="category" onchange="loadSubCategories()">
    <option value="">Select Category</option>
  </select>

  <br><br>

  <label>Sub-Category:</label>
  <select id="subcategory">
    <option value="">Select Sub-category</option>
  </select>
</form>

<script>
// Data structure
const data = {
  fixed: {
    "Loans": ["Long Term Loan", "Short Term Loan"],
    "Deposits": ["Fixed Deposit", "Recurring Deposit"]
  },
  current: {
    "Sundry Creditors": ["Creditors for Goods", "Creditors for Expenses"],
    "Outstanding Expenses": ["Salary Payable", "Rent Payable"]
  }
};

function loadCategories() {
  const liability = document.getElementById("liability").value;
  const categorySelect = document.getElementById("category");
  const subcategorySelect = document.getElementById("subcategory");

  // Reset
  categorySelect.innerHTML = "<option value=''>Select Category</option>";
  subcategorySelect.innerHTML = "<option value=''>Select Sub-category</option>";

  if (liability && data[liability]) {
    const categories = Object.keys(data[liability]);
    categories.forEach(cat => {
      const option = document.createElement("option");
      option.value = cat;
      option.text = cat;
      categorySelect.appendChild(option);
    });
  }
}

function loadSubCategories() {
  const liability = document.getElementById("liability").value;
  const category = document.getElementById("category").value;
  const subcategorySelect = document.getElementById("subcategory");

  // Reset
  subcategorySelect.innerHTML = "<option value=''>Select Sub-category</option>";

  if (liability && category && data[liability][category]) {
    const subcategories = data[liability][category];
    subcategories.forEach(sub => {
      const option = document.createElement("option");
      option.value = sub;
      option.text = sub;
      subcategorySelect.appendChild(option);
    });
  }
}
</script>

</body>
</html>
