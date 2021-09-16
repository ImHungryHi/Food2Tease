
          <div class="container">
            <a href="{$browseLink}">&lt; Verderwinkelen</a>
            <h2 class="title is-2">Bedankt voor je bestelling!</h2>
            <h3 class="subtitle is-3">Wij nemen het verder over. Wil je <a href="{$browseLink}">verderwinkelen</a>?</h3>

            <div class="columns is-multiline is-12 is-gapless">
              <div class="column hero has-border-bottom is-12 parentColumn titleColumn">
                <div class="columns is-gapless level">
                  <div class="column level-item is-7 has-text-centered">
                    <p>Info</p>
                  </div>
                  <div class="column level-item is-3 has-text-centered">
                    <p>Prijs</p>
                  </div>
                  <div class="column level-item is-1 has-text-centered">
                    <p>Aantal</p>
                  </div>
                  <div class="column level-item is-1 has-text-centered">
                    <p>Subtotaal</p>
                  </div>
                </div>
              </div>

              <div class="column hero has-border-bottom is-12 parentColumn">
                {iteration:iItems}
                <div class="columns is-gapless level grid-box childColumn">
                  <div class="column level-item is-7 grid-1">
                    <h2 class="title is-4">{$artTitle}{option:oHasSauce} met {$artSauce}{/option:oHasSauce}</h2>
                    {option:oHasExtras}<p>Met extra {$extraContent}</p>{/option:oHasExtras}
                  </div>

                  <div class="column level-item is-3 has-text-centered grid-2">
                    <strong class="hero is-1">&nbsp;&nbsp;&nbsp;&nbsp;&euro; {$artPrice}{option:oExtraCondimentPrice}<br />+ &euro; {$extraCondimentPrice}{/option:oExtraCondimentPrice}{option:oFriesPrice}<br />+ &euro; {$friesPrice}{/option:oFriesPrice}</strong>
                  </div>

                  <div class="column level-item is-1 has-text-centered grid-3">
                    <strong>{$artQuantity}</strong>
                  </div>

                  <div class="column level-item is-1 has-text-centered grid-4">
                   <strong>&euro; {$artTotal}</strong>
                 </div>
               </div>
                {/iteration:iItems}
              </div>

              <div class="column hero has-border-bottom is-12 parentColumn">
                <div class="columns is-gapless level">
                  <div class="column level-item is-8-tablet is-12-mobile">&nbsp;</div>
                  <div class="column level-item is-2-tablet is-5-mobile has-text-centered is-left-mobile">
                    <strong>Totaal:</strong>
                  </div>
                  <div class="column level-item is-2-tablet has-text-centered is-5-mobile is-right-mobile">
                    <strong>&euro; {$checkoutTotal}</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
