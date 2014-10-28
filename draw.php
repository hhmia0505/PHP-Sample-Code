<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
      Pie chart
    </title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript" src="js/pieJS.js"></script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
    <div class="tab-pane active" id="column">Column Selection<br>
      <label class="radio">
      <input type="radio" value="Households" name="pieColumn" id="pieColumn1"/>
        Households
      </label>
      <label class="radio">
      <input type="radio" value="Family Households" name="pieColumn" id="pieColumn2"/>
      Family Households
      </label>
      <label class="radio">
      <input type="radio" value="Married Couples" name="pieColumn" id="pieColumn3"/>
      Married Couples
      </label>
      <label class="radio">
      <input type="radio" value="Family Male Households" name="pieColumn" id="pieColumn4"/>
      Family Male Households
      </label>
      <label class="radio">
      <input type="radio" value="Family Female Households" name="pieColumn" id="pieColumn5"/>
      Family Female Households
      </label>
      <label class="radio">
      <input type="radio" value="Nonfamily Households" name="pieColumn" id="pieColumn6"/>
      Nonfamily Households
      </label>
      <label class="radio">
      <input type="radio" value="Nonfamily Male Households" name="pieColumn" id="pieColumn7"/>
      Nonfamily Male Households
      </label>
      <label class="radio">
      <input type="radio" value="Nonfamily Female Households" name="pieColumn" id="pieColumn8"/>
      Nonfamily Female Households
      </label>
      </div>
    <div class="tab-pane" id="value">Summarize Type<br>
      <label class="radio">
      <input type="radio" value="Count" name="type" id="type1"/>
      Count
      </label>
      <label class="radio">
      <input type="radio" value="Sum" name="type" id="type2"/>
      Sum
      </label>
      </div>
    <button onclick="drawPie()">Save changes</button>
    <div id="motionResult"></div>
  </body>
</html>