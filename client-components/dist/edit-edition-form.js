 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state =
    {
        id: null,
        magazine_id: null,
        ref_date: null,
        edition_label: '',
        title: '',
        description: '',
        is_published: 0,
        is_open_for_submissions: 0,
        lang: {}
    };

    const methods =
    {
        changeField(e)
        {
            if (e.target.type === 'checkbox')
                this.render({ ...this.state, [e.target.name]: Number(!Number(this.state[e.target.name])) });
            else
                this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        create(data)
        {
            import(AbelMagazine.functionUrl(`/admin/panel/magazines/${this.state.magazine_id}/editions`))
            .then(module => module.create(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(([ ret, json ]) =>
                {
                    if (json.success && json.newId)
                        window.location.href = AbelMagazine.Helpers.URLGenerator.generatePageUrl(`/admin/panel/magazines/${this.state.magazine_id}/editions/${json.newId}`);
                })
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorCreateEdition));
        },

        edit(data)
        {
            import(AbelMagazine.functionUrl(`/admin/panel/magazines/${this.state.magazine_id}/editions/${this.state.id}`))
            .then(module => module.edit(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorEditEdition));
        },

        submit(e)
        {
            e.preventDefault();

            const data = {};
            for (const prop in this.state)
                if (prop !== 'lang')
                    data['editions:' + prop] = this.state[prop];

            if (this.state.id)
                this.edit(data);
            else
                this.create(data);
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.refDate}`}, [
        h("input", {"type": `date`, "required": ``, "name": `ref_date`, "onchange": this.changeField.bind(this), "value": state.ref_date, "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.title}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "required": ``, "name": `title`, "onchange": this.changeField.bind(this), "value": state.title, "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.description}`, "linebreak": `1`}, [
        h("textarea", {"name": `description`, "class": `w-full`, "row": `5`, "maxlength": `2000`, "onchange": this.changeField.bind(this), "value": state.description}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.editionLabel}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "required": ``, "name": `edition_label`, "placeholder": `Ex.: Ano 1, número 1`, "onchange": this.changeField.bind(this), "value": state.edition_label, "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.isEditionPublished}`, "reverse": `1`}, [
        h("input", {"type": `checkbox`, "name": `is_published`, "onchange": this.changeField.bind(this), "checked": Boolean(Number(state.is_published))}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.isEditionOpen}`, "reverse": `1`}, [
        h("input", {"type": `checkbox`, "name": `is_open_for_submissions`, "onchange": this.changeField.bind(this), "checked": Boolean(Number(state.is_open_for_submissions))}, "")
      ]),
      h("div", {"class": `mt-2 text-center`}, [
        h("button", {"type": `submit`, "class": `btn`}, `${state.lang.forms.save}`)
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
