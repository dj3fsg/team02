/**
 * public/js/account.js
 * - 繰り返し期限のON/OFF
 * - 区分（入金/出金：subcategory_id=3/4）選択時にカテゴリ<select>を絞り込み（optgroupごと作り直し）
 * - バリデーションエラー時：old(account_category_id) を復元
 */
document.addEventListener('DOMContentLoaded', function () {

  console.log('[account.js] loaded');
 
  const repeatSelect = document.getElementById('repeat_id');
  const repeatUntil = document.getElementById('repeat_until');
  

  function toggleRepeatUntil() {
    if (!repeatSelect || !repeatUntil) return;

    if (repeatSelect.value === '0') {
      repeatUntil.disabled = true;
      repeatUntil.value = '';
    } else {
      repeatUntil.disabled = false;
    }
  }

  if (repeatSelect) {
    toggleRepeatUntil();
    repeatSelect.addEventListener('change', toggleRepeatUntil);
  }

  /* =========================
   * 区分（3/4）でカテゴリ絞り込み（old復元対応）
   * ========================= */
  const radios = document.querySelectorAll('input[name="subcategory_id"]');
  const categorySelect = document.getElementById('account_category_id');
  if (!categorySelect) {
    return;
  }

  // old のカテゴリID（バリデーションエラーから戻った時用）
  const oldCategoryId = categorySelect.dataset.old || '';

  // 「選択してください」(value="") を保持
  const placeholder =
    categorySelect.querySelector('option[value=""]')?.cloneNode(true) ||
    new Option('選択してください', '');

  // optgroupを退避（Blade側のラベルに合わせる）
  const inGroupOriginal = categorySelect.querySelector('optgroup[label="入金"]')?.cloneNode(true);
  const outGroupOriginal = categorySelect.querySelector('optgroup[label="出金"]')?.cloneNode(true);

  // optgroupが無い場合の保険（data-kbn="in/out"）
  const allOptionsOriginal = Array.from(categorySelect.querySelectorAll('option'))
    .filter((opt) => opt.value !== '');

  function buildOptgroup(label, options) {
    const og = document.createElement('optgroup');
    og.label = label;
    options.forEach((opt) => og.appendChild(opt.cloneNode(true)));
    return og;
  }

  /**
   * @param {string|null} subcategoryId '3'(入金) / '4'(出金) / null(未選択)
   * @param {{keepSelection:boolean}} opts
   */
  function rebuildCategories(subcategoryId, opts = { keepSelection: false }) {
    // keepSelection=true の場合は、今の選択 or old を復元したい
    const desiredValue = opts.keepSelection ? (categorySelect.value || oldCategoryId) : '';

    // 作り直し
    categorySelect.innerHTML = '';
    categorySelect.appendChild(placeholder.cloneNode(true));

    if (subcategoryId === '3') {
      // 入金
      if (inGroupOriginal) {
        categorySelect.appendChild(inGroupOriginal.cloneNode(true));
      } else {
        const o = allOptionsOriginal.filter((x) => x.dataset.kbn === 'in');
        if (o.length) categorySelect.appendChild(buildOptgroup('入金', o));
      }
    } else if (subcategoryId === '4') {
      // 出金
      if (outGroupOriginal) {
        categorySelect.appendChild(outGroupOriginal.cloneNode(true));
      } else {
        const o = allOptionsOriginal.filter((x) => x.dataset.kbn === 'out');
        if (o.length) categorySelect.appendChild(buildOptgroup('出金', o));
      }
    } else {
      // 未選択：両方
      if (inGroupOriginal) categorySelect.appendChild(inGroupOriginal.cloneNode(true));
      if (outGroupOriginal) categorySelect.appendChild(outGroupOriginal.cloneNode(true));
    }

    // 復元（存在するoptionなら戻す）
    if (desiredValue) {
      const exists = !!categorySelect.querySelector(`option[value="${CSS.escape(desiredValue)}"]`);
      categorySelect.value = exists ? desiredValue : '';
    } else {
      categorySelect.value = '';
    }
  }

  // 区分変更：イベント委譲（DOM後挿し・タイミングズレ対策）
  document.addEventListener('change', (e) => {
    if (!e.target.matches('input[name="subcategory_id"]')) return;
    rebuildCategories(e.target.value, { keepSelection: false });
  });


  // 初期反映：checked の区分に合わせて絞り込みつつ、oldカテゴリを復元
  const checked = document.querySelector('input[name="subcategory_id"]:checked');
  rebuildCategories(checked ? checked.value : null, { keepSelection: true });
});
