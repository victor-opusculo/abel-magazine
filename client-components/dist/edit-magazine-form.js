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
        name: '',
        description: '',
        cover_image_media_id: null,
        string_identifier: '',
        issn: null,
        search_media_enabled: false,
        lang: {}
    };

    const methods = 
    {
        changeField(e)
        {
            this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        toggleMediaSelect(e)
        {
            this.render({ ...this.state, search_media_enabled: !this.state.search_media_enabled });
        },

        create(formData)
        {
            import(AbelMagazine.functionUrl('/admin/panel/magazines'))
            .then(module => module.create(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(([ ret, json ]) =>
                {
                    if (json.success && json.newId)
                        window.location.href = AbelMagazine.Helpers.URLGenerator.generatePageUrl('/admin/panel/magazines/' + json.newId);
                })
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorCreateMagazine));
        },

        edit(formData)
        {
            import(AbelMagazine.functionUrl(`/admin/panel/magazines/${this.state.id}`))
            .then(module => module.edit(formData))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorEditMagazine));
        },

        submit(e)
        {
            e.preventDefault();

            const data = {};
            for (const prop in this.state)
                if (prop !== "lang" && prop !== "search_media_enabled")
                    data['magazines:' + prop] = this.state[prop];

            if (this.state.id)
                this.edit(data);
            else
                this.create(data);
        }
    };

    function setup()
    {
        this.render({ ...this.state, lang: JSON.parse(this.getAttribute('langJson')) });
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.name}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "class": `w-full`, "name": `name`, "oninput": this.changeField.bind(this), "required": ``, "value": state.name}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.description}`, "linebreak": `1`}, [
        h("textarea", {"type": `text`, "maxlength": `400`, "class": `w-full`, "rows": `5`, "name": `description`, "oninput": this.changeField.bind(this), "value": state.description}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.stringIdentifier}`}, [
        h("input", {"type": `text`, "maxlength": `140`, "class": `w-full`, "name": `string_identifier`, "oninput": this.changeField.bind(this), "required": ``, "value": state.string_identifier, "pattern": `^[A-Za-z0-9_\\-]{5,140}$`, "placeholder": `${state.lang.forms.noSpacesAllowedMin5chars}`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.issn}`}, [
        h("input", {"type": `text`, "maxlength": `50`, "class": `w-full`, "name": `issn`, "oninput": this.changeField.bind(this), "required": ``, "value": state.issn}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.logoImagePictureMediaId}`}, [
        h("input", {"type": `number`, "min": `1`, "step": `1`, "class": `w-[calc(100%-200px)] mr-2`, "name": `cover_image_media_id`, "oninput": this.changeField.bind(this), "value": state.cover_image_media_id}, ""),
        h("button", {"type": `button`, "class": `btn`, "onclick": this.toggleMediaSelect.bind(this)}, `${state.lang.forms.search}`)
      ]),
      ((state.search_media_enabled) ? h("media-client-select", {"lang": state.lang, "set_id_field_callback": id => this.changeField({ target: { name: 'cover_image_media_id', value: id } })}, "") : ''),
      h("div", {"class": `my-2 text-center`}, [
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

  
