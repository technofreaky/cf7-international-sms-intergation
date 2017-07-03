<div id="cf7si-sms-sortables" class="meta-box-sortables ui-sortable">
    <?php
        foreach($fields as $id  => $f){
            echo '<h3>'.$f['name'].'</h3>';
            echo '<fieldset>';
                if(!empty($f['info']))
                    echo '<legend>'.$f['info'].'</legend>';
            
                echo '<table class="form-table"> <tbody>';
                foreach($f['fields'] as $fi => $fv){
                    $desc = isset($fv['description'])? $fv['description'] : '';
                    echo '<tr>';
                        echo '<th scope="row"> <label for="'.$fi.'">'.$fv['label'].'</label> </th>';
                        echo '<td>';
                            cf7_isms_form_field($fi,$fv);
                        echo '<p>'.$desc.'</p>';
                        echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody> </table> ';  
            echo '</fieldset> <hr/>';
        }
    ?>
    
</div>