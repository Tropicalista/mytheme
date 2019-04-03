<?php

        $err = array();
        
        /* --------------------------------------------------- *
         * Set Form DEFAULT values
         * --------------------------------------------------- */
        $freq                            = 12;
        $default_sale_price              = "";
        $default_annual_interest_percent = "";
        $default_year_term               = "";
        $default_down_percent            = "";
        $default_show_progress           = TRUE;
        /* --------------------------------------------------- */
        


        /* --------------------------------------------------- *
         * Initialize Variables
         * --------------------------------------------------- */
        $sale_price                      = 0;
        $annual_interest_percent         = 0;
        $year_term                       = 0;
        $down_percent                    = 0;
        $this_year_interest_paid         = 0;
        $this_year_principal_paid        = 0;
        $form_complete                   = false;
        $show_progress                   = true;
        $monthly_payment                 = false;
        $error                           = false;
        /* --------------------------------------------------- */

        if ( isset( $_POST['form_complete'] ) ) {

            $sale_price                         = $_POST['sale_price'];
            $annual_interest_percent            = str_replace(',', '.', sanitize_text_field($_POST['annual_interest_percent']));
            $year_term                          = $_POST['year_term'];
            $freq                               = $_POST['freq'];
            $show_progress                      = (isset($_POST['show_progress'])) ? sanitize_text_field($_POST['show_progress']) : false;
            $form_complete                      = $_POST['form_complete'];


            if( !is_numeric($sale_price) ){
                $err['sale_price'] = "Per favore inserisci un numero";
            }

            if( !is_numeric($annual_interest_percent) ){
                $err['annual_interest_percent'] = "Per favore inserisci un numero";
            }

            if( !filter_var($year_term, FILTER_VALIDATE_INT) ){
                $err['year_term'] = "Per favore inserisci un numero senza punti nè virgole";
            }

            if( !empty($err) ){
                $form_complete = false;
            }

            // If the form is complete, we'll start the math
            if ( $form_complete ) {
                // We'll set all the numeric values to JUST
                // numbers - this will delete any dollars signs,
                // commas, spaces, and letters, without invalidating
                // the value of the number
                $sale_price              = preg_replace( "[^0-9.]", "", $sale_price);
                $annual_interest_percent = preg_replace("[^0-9.]", "", $annual_interest_percent);
                $year_term               = preg_replace("[^0-9.]", "", $year_term);
                
                if (((float) $year_term <= 0) || ((float) $sale_price <= 0) || ((float) $annual_interest_percent <= 0)) {
                    $error = "Per favore devi inserire l'importo del prestito, il tasso e la durata.";
                }

                if (!$error) {
                    $month_term              = $year_term * $freq;
                    $annual_interest_rate    = $annual_interest_percent / 100;
                    $monthly_interest_rate   = $annual_interest_rate / $freq;
                }
            } else {
                if (!$sale_price)              { $sale_price              = $default_sale_price;              }
                if (!$annual_interest_percent) { $annual_interest_percent = $default_annual_interest_percent; }
                if (!$year_term)               { $year_term               = $default_year_term;               }
                if (!$show_progress)           { $show_progress           = $default_show_progress;           }
            }

        }
?>

<div class="inside-article">

    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <p>Usa il form sottostante per effettuare il calcolo della rata del prestito. Tutti i campi sono obbligatori.</p>

    <form action="<?php esc_url( $_SERVER['REQUEST_URI'] ) ;?>" class="calcolo-form" method="post">

            <input type="hidden" name="form_complete" value="1">
            <label for="sale_price">Importo del prestito:</label>
            <input type="number" step="0.01" min="0" name="sale_price" value="<?php echo ( isset( $_POST["sale_price"] ) ? esc_attr( $_POST["sale_price"] ) : '' ) ; ?>" required />
            <?php 
            if(array_key_exists('sale_price',$err) ){
                echo "<p class='text-danger'>" . $err['sale_price'] . "</p>";
            };
            ?>          

            <label for="year_term">Durata (anni):</label>
            <input type="number" name="year_term" value="<?php echo ( isset( $_POST["year_term"] ) ? esc_attr( $_POST["year_term"] ) : '' ) ; ?>" required />
            <?php 
            if(array_key_exists('year_term',$err) ){
                echo "<p class='text-danger'>" . $err['year_term'] . "</p>";
            };
            ?>          

            <label for="freq">Frequenza rate:</label>
            <select name="freq">
              <option value="12" <?php if($freq == 12){ echo "selected"; };?>>mensile</option>
              <option value="4" <?php if($freq == 4){ echo "selected"; };?>>trimestrale</option>
              <option value="2" <?php if($freq == 2){ echo "selected"; };?>>semestrale</option>
            </select>

            <label for="annual_interest_percent">Tasso d'interesse (usa il punto per i decimali):</label>
            <input type="number" step="0.01" min="0" name="annual_interest_percent" value="<?php echo ( isset( $_POST["annual_interest_percent"] ) ? esc_attr( $_POST["annual_interest_percent"] ) : '' ) ; ?>" required />
            <?php 
            if(array_key_exists('annual_interest_percent',$err) ){
                echo "<p class='text-danger'>" . $err['annual_interest_percent'] . "</p>";
            };
            ?>          

            <label><input name="show_progress" type="checkbox" <?php if ($show_progress) { print("checked"); } ?>> Visualizza il piano di ammortamento</label>

        <input type="submit" class="btn btn-default" value="Calcola" />

    </form>

                    <?php
                        // If the form has already been calculated, the $down_payment
                        // and $monthly_payment variables will be figured out, so we
                        // can show them in this table
                        if ($form_complete) {

                            // Set some base variables
                            $principal     = $sale_price;
                            $current_month = 1;
                            $current_year  = 1;
                            // This basically, re-figures out the monthly payment, again.
                            $power = -($month_term);
                            $denom = pow((1 + $monthly_interest_rate), $power);
                            $monthly_payment = $principal * ($monthly_interest_rate / (1 - $denom));

                            $totale = $monthly_payment * $year_term * $freq;
                            $interessi = $totale-$sale_price;
                            $perc_interessi = ($interessi/$totale)*100;
                    ?>


                            <p id="rata"><strong>La tua rata:  <?php echo "€ " . number_format($monthly_payment, "2", ".", ","); ?></strong></p>
                            <p id="sum">Costo totale del finanziamento: <b><?php echo "€ " . number_format(($monthly_payment * $year_term * $freq), "2", ".", ","); ?></b> di cui <b><?php echo "€ " . number_format($sale_price, "2", ".", ","); ?></b> di capitale e <b><?php echo "€ " . number_format($interessi, "2", ".", ","); ?></b> di interessi, pari al <?php echo number_format($perc_interessi, "2", ".", ","); ?>%.</p>


                    <?php    
                        }
                    ?>        

                    <?php
                        // This prints the calculation progress and 
                        // the instructions of HOW everything is figured
                        // out
                        if ($form_complete && $show_progress) {
                            $step = 1;
                    ?>
                                <?php print("<p><a name=\"amortization\"></a>Piano di ammortamento: <b>€" . number_format($monthly_payment, "2", ".", ",") . "</b> per " . $year_term . " anni con " . $freq . " rate per anno.</p>"); ?>

                            
                    <?php
                            
                            print("<table class=\"table\">");
                            
                            // This LEGEND will get reprinted every $freq months
                            $legend  = "<tr>";
                            $legend .= "<td scope=\"col\"><b>Mese</b></td>";
                            $legend .= "<td scope=\"col\"><b>Interessi</b></td>";
                            $legend .= "<td scope=\"col\"><b>Capitale</b></td>";
                            $legend .= "<td scope=\"col\"><b>Capitale residuo</b></td>";
                            $legend .= "</tr>";
                            
                            echo $legend;
                                    
                            // Loop through and get the current month's payments for 
                            // the length of the loan 
                            while ($current_month <= $month_term) {        
                                $interest_paid     = $principal * $monthly_interest_rate;
                                $principal_paid    = $monthly_payment - $interest_paid;
                                $remaining_balance = $principal - $principal_paid;
                                
                                $this_year_interest_paid  = $this_year_interest_paid + $interest_paid;
                                $this_year_principal_paid = $this_year_principal_paid + $principal_paid;
                                
                                print("<tr>");
                                print("<td>" . $current_month . "</td>");
                                print("<td>€" . number_format($interest_paid, "2", ".", ",") . "</td>");
                                print("<td>€" . number_format($principal_paid, "2", ".", ",") . "</td>");
                                print("<td>€" . number_format($remaining_balance, "2", ".", ",") . "</td>");
                                print("</tr>");
                        
                                ($current_month % $freq) ? $show_legend = FALSE : $show_legend = TRUE;
                        
                                if ($show_legend) {
                                    print("<tr valign=\"top\" bgcolor=\"#ffffcc\">");
                                    print("<td colspan=\"4\"><b>Totale per anno " . $current_year . "</td>");
                                    print("</tr>");
                                    
                                    $total_spent_this_year = $this_year_interest_paid + $this_year_principal_paid;
                                    print("<tr valign=\"top\" bgcolor=\"#ffffcc\">");
                                    print("<td colspan=\"4\">");
                                    print("Spenderai € <b>" . number_format($total_spent_this_year, "2", ".", ",") . "</b> per il " . $current_year . "° anno.<br>");
                                    print(" di cui € <b>" . number_format($this_year_interest_paid, "2", ".", ",") . "</b> di INTERESSI.<br>");
                                    print(" e € <b>" . number_format($this_year_principal_paid, "2", ".", ",") . "</bs> di CAPITALE.<br>");
                                    print("</td>");
                                    print("</tr>");
                        
                                    print("<tr valign=\"top\" bgcolor=\"#ffffff\">");
                                    print("<td colspan=\"4\">&nbsp;<br><br></td>");
                                    print("</tr>");
                                    
                                    $current_year++;
                                    $this_year_interest_paid  = 0;
                                    $this_year_principal_paid = 0;
                                    
                                    if (($current_month + 6) < $month_term) {
                                        echo $legend;
                                    }
                                }
                        
                                $principal = $remaining_balance;
                                $current_month++;
                            }
                            print("</table>");
                            ?>
                       <?php
                        }
                    ?>
</div>                 
