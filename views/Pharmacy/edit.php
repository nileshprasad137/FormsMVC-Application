<form action="Pharmacy/save" method="post"> 
    Name::  <input name="pharmacy_name" type="text" > <br><br>
    Address::  <input name="pharmacy_address" type="text" > <br> <br>
    Email :: <input name="pharmacy_email" type="text" > <br> <br>
    City :: <input name="pharmacy_city" type="text" > <br> <br>
    State:: <input name="pharmacy_state" type="text" > <br><br>
    Zip:: <input name="pharmacy_zip" type="text" > <br><br>
    Phone :: <input name="pharmacy_phone" type="text" > <br><br>
    Fax :: <input name="pharmacy_fax" type="text" > <br><br>
    Default Method:: 
    <select name="default_method">
        <option value="Print">Print</option>
        <option value="Email">Email</option>
        <option value="Fax">Fax</option>
  </select>
    <br><br>
    <input type="submit" name="submit_pharmacy" value="Submit">
</form>
    