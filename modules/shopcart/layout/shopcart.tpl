
          <div class="container">
            <form action="{$formUrl|htmlentities}" enctype="multipart/form-data" method="post">
              <a onclick="updateItems()">&lt; Verderwinkelen</a>
              <h2 class="title is-2">Winkelwagentje</h2>

              {option:oHasNoItems}
              <div class="notification has-text-centered">
                <h3 class="subtitle is-4">Hier is niets te zien, wil je <a onclick="updateItems()">verderwinkelen</a>?</h3>
                <dl class="hidden">
                  <dd>
                    <label for="btnUpdate"><button id="btnUpdate" name="action" value="updateAndBack">Verderwinkelen</button></label>
                  </dd>
                </dl>
              </div>
              {/option:oHasNoItems}
              {option:oHasItems}
              <div class="columns is-multiline is-12 is-gapless">
                <div class="column hero has-border-bottom is-12 parentColumn titleColumn">
        					<div class="columns is-gapless level">
        						<div class="column level-item is-12-mobile">
        							&nbsp;
        						</div>
        						<div class="column level-item">
        							<p>Info</p>
        						</div>
        						<div class="column level-item has-text-centered">
        							<p>Prijs</p>
        						</div>
        						<div class="column level-item has-text-centered">
        							<p>Aantal</p>
        						</div>
        						<div class="column level-item has-text-centered">
        							<p>Subtotaal</p>
        						</div>
        						<div class="column level-item has-text-centered is-narrow is-1">
        							&nbsp;
        						</div>
                	</div>
                </div>

                <div class="column hero has-border-bottom is-12 parentColumn">
                  {iteration:iItems}
                  <div class="columns is-gapless level grid-box childColumn">
        						<div class="column level-item is-12-mobile grid-1">
                      <figure class="image is-128x128"><img src="modules/shopcart/img/placeholder.jpg" alt="placeholder" /></figure>
        						</div>

                    <div class="column level-item grid-2">
                      <h2 class="title is-4">{$artTitle}{option:oHasSauce} met <span>{$artSauce}</span>{/option:oHasSauce}</h2>
                      {option:oHasDescription}
                      <p>{$artDescription}</p>
                      {/option:oHasDescription}
                      {option:oHasSauces}
                      <div class="tile">
                        <label for="selSauceFor&lowbar;{$itemId}">
                          <select id="selSauceFor&lowbar;{$itemId}" name="selSauceFor&lowbar;{$itemId}" class="select is-small">
                            {iteration:iSauces}
                            <option value="{$sauceValue}"{option:oSauceSelected} selected{/option:oSauceSelected}>{$sauceText}</option>
                            {/iteration:iSauces}
                          </select>
                        </label>
                      </div>
                      {/option:oHasSauces}
                      {option:oHasExtraSauces}
                      <div class="tile">
                        <label for="selExtraSauceFor&lowbar;{$itemId}">
                          <select id="selExtraSauceFor&lowbar;{$itemId}" name="selExtraSauceFor&lowbar;{$itemId}" onchange="updateSauceType(&quot;{$itemId}&quot;)" class="select is-small">
                            <option value="0"{option:oNoExtraSauceSelected} selected{/option:oNoExtraSauceSelected}>Extra saus &ndash; optioneel</option>
                            {iteration:iExtraSauces}
                            <option value="{$extraSauceValue}"{option:oExtraSauceSelected} selected{/option:oExtraSauceSelected}>{$extraSauceText} &ndash; &euro; {$extraCondimentPrice}</option>
                            {/iteration:iExtraSauces}
                          </select>
                        </label>
                      </div>
                      {/option:oHasExtraSauces}
                      <div class="tile">
                        <label for="chkExtraFriesFor_{$itemId}" class="checkbox">
                          <input type="checkbox" id="chkExtraFriesFor_{$itemId}" onchange="updateFries(&quot;{$itemId}&quot;)" name="chkExtraFriesFor_{$itemId}" value="{$extraFriesValue}" {option:oExtraFriesChecked}checked {/option:oExtraFriesChecked}/>
                          {$extraFriesText} (+ &euro; <span id="extraPrice_{$itemId}">{$extraFriesPrice}</span>)
                        </label>
                      </div>
                    </div>

                    <div class="column level-item has-text-centered grid-3">
                      <strong>&euro; <span id="itemPrice&lowbar;{$itemId}">{$artPrice}</span><span class="informators"> / eenheid</span></strong>
                    </div>

                    <div class="column level-item has-text-centered is-5-mobile grid-4">
                      <label for="selQuantityFor&lowbar;{$itemId}"><input type="number" id="selQuantityFor&lowbar;{$itemId}" name="selQuantityFor&lowbar;{$itemId}" class="is-number input is-small itemQuantity" min="1" value="{$artQuantity}" onchange="updateQuantity(&quot;{$itemId}&quot;)" /></label>
                    </div>

                    <div class="column level-item has-text-centered grid-5">
                      <p><strong><span class="informators">Totaal: </span><span id="itemTotal&lowbar;{$itemId}" class="itemTotal">&euro; {$artTotal}</span></strong></p>
                    </div>

                    <div class="column level-item has-text-centered is-narrow is-1 grid-6">
                      <a class="button is-danger" onclick="deleteArticle(&quot;{$itemId}&quot;)" data-placement="bottom" data-toggle="tooltip" title="Verwijderen"><i class="fa fa-trash"></i></a>
                      <label for="btnDeleteFor&lowbar;{$itemId}"><button id="btnDeleteFor&lowbar;{$itemId}" name="action" class="hidden" value="btnDeleteFor&lowbar;{$itemId}">Verwijderen</button></label>
                    </div>
                  </div>
                  {/iteration:iItems}
                </div>

                <div class="column hero has-border-bottom is-12 parentColumn">
        					<div class="columns is-gapless level">
        						<div class="column level-item is-12-mobile">
        							&nbsp;
        						</div>
        						<div class="column level-item is-hidden-mobile">
        							&nbsp;
        						</div>
        						<div class="column level-item is-hidden-mobile">
        							&nbsp;
        						</div>
        						<div class="column level-item has-text-centered is-5-mobile is-left-mobile">
        							<strong>Totaal:</strong>
        						</div>
        						<div class="column level-item has-text-centered is-5-mobile is-right-mobile">
        							<strong><span id="grandTotal">&euro; {$shopcartTotal}</span></strong>
        						</div>
        						<div class="column level-item has-text-centered is-narrow is-1 is-hidden-mobile">
        							&nbsp;
        						</div>
                	</div>
                </div>
              </div>

              <div class="bottomPart content">
                <div id="comments">
                  <label for="txtComment"><textarea id="txtComment" class="textarea" name="txtComment" placeholder="Extra vragen en info kan je hier kwijt...">{$txtCommentContent}</textarea></label>
                </div>
                <div id="submit">
                  <button id="btnCheckout" class="button is-medium is-info" name="action" value="checkout">Verder</button>
                  <button id="btnUpdate" class="button is-medium is-info is-inverted" name="action" value="updateAndBack">Verderwinkelen</button>
                </div>
              </div>
              {/option:oHasItems}
            </form>
          </div>
